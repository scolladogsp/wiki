<?php

/**
 * Database backend for echo notification
 */
class MWDbEchoBackend extends MWEchoBackend {

	/**
	 * @param $row array
	 */
	public function createNotification( $row ) {
		$dbw = MWEchoDbFactory::getDB( DB_MASTER );

		$fname = __METHOD__;
		$dbw->onTransactionIdle(
			function() use ( $dbw, $row, $fname ) {
				$dbw->begin( $fname );
				// reset the base if this notification has a display hash
				if ( $row['notification_bundle_display_hash'] ) {
					$dbw->update(
						'echo_notification',
						array( 'notification_bundle_base' => 0 ),
						array(
							'notification_user' => $row['notification_user'],
							'notification_bundle_display_hash' => $row['notification_bundle_display_hash'],
							'notification_bundle_base' => 1
						),
						$fname
					);
				}

				$row['notification_timestamp'] = $dbw->timestamp( $row['notification_timestamp'] );
				$dbw->insert( 'echo_notification', $row, $fname );
				$dbw->commit( $fname );

				$user = User::newFromId( $row['notification_user'] );
				if ( !$user->isAnon() ) {
					MWEchoNotifUser::newFromUser( $user )->resetNotificationCount( DB_MASTER );
				}
			}
		);
	}

	/**
	 * @param $user User the user to get notifications for
	 * @param $limit int The maximum number of notifications to return
	 * @param $continue string Used for offset
	 * @param $outputFormat string The output format of the notifications (web,
	 *    email, etc.)
	 * @return array
	 */
	public function loadNotifications( $user, $limit, $continue, $outputFormat = 'web' ) {
		$dbr = MWEchoDbFactory::getDB( DB_SLAVE );

		$eventTypesToLoad = EchoNotificationController::getUserEnabledEvents( $user, $outputFormat );
		if ( !$eventTypesToLoad ) {
			return array();
		}

		// Look for notifications with base = 1
		$conds = array(
			'notification_user' => $user->getID(),
			'event_type' => $eventTypesToLoad,
			'notification_bundle_base' => 1
		);

		$offset = $this->extractQueryOffset( $continue );

		// Start points are specified
		if ( $offset['timestamp'] && $offset['offset'] ) {
			$ts = $dbr->addQuotes( $dbr->timestamp( $offset['timestamp'] ) );
			// The offset and timestamp are those of the first notification we want to return
			$conds[] = "notification_timestamp < $ts OR ( notification_timestamp = $ts AND notification_event <= " . $offset['offset'] . " )";
		}

		$res = $dbr->select(
			array( 'echo_notification', 'echo_event' ),
			'*',
			$conds,
			__METHOD__,
			array(
				'ORDER BY' => 'notification_timestamp DESC, notification_event DESC',
				'LIMIT' => $limit,
			),
			array(
				'echo_event' => array( 'LEFT JOIN', 'notification_event=event_id' ),
			)
		);

		return iterator_to_array( $res, false );
	}

	/**
	 * @param $user User
	 * @param $bundleHash string the bundle hash
	 * @param $type string
	 * @param $order string 'ASC'/'DESC'
	 * @param $limit int
	 * @return ResultWrapper|bool
	 */
	public function getRawBundleData( $user, $bundleHash, $type = 'web', $order = 'DESC', $limit = 250 ) {
		$dbr = MWEchoDbFactory::getDB( DB_SLAVE );

		// We only display 99+ if the number is over 100, we can do limit 250, this should be sufficient
		// to return 99 distinct group iterators, avoid select count( distinct ) for the following reason:
		// 1. it will not scale for large volume data
		// 2. notification may have random grouping iterator
		// 3. agent may be anonymous, can't do distinct over two columns: event_agent_id and event_agent_ip
		if ( $type == 'web' ) {
			$res = $dbr->select(
				array( 'echo_notification', 'echo_event' ),
				array( 'event_agent_id', 'event_agent_ip', 'event_extra', 'event_page_id' ),
				array(
					'notification_event=event_id',
					'notification_user' => $user->getId(),
					'notification_bundle_base' => 0,
					'notification_bundle_display_hash' => $bundleHash
				),
				__METHOD__,
				array( 'ORDER BY' => 'notification_timestamp ' . $order, 'LIMIT' => $limit )
			);
		// this would be email for now
		} else {
			$res = $dbr->select(
				array( 'echo_email_batch', 'echo_event' ),
				array( 'event_agent_id', 'event_agent_ip', 'event_extra', 'event_page_id' ),
				array(
					'eeb_event_id=event_id',
					'eeb_user_id' => $user->getId(),
					'eeb_event_hash' => $bundleHash
				),
				__METHOD__,
				array( 'ORDER BY' => 'eeb_event_id ' . $order, 'LIMIT' => $limit )
			);
		}

		return $res;
	}

	/**
	 * Get the last bundle stat - read_timestamp & bundle_display_hash
	 * @param $user User
	 * @param $bundleHash string The hash used to identify a set of bundle-able events
	 * @return ResultWrapper|bool
	 */
	public function getLastBundleStat( $user, $bundleHash ) {
		$dbr = MWEchoDbFactory::getDB( DB_SLAVE );

		$res = $dbr->selectRow(
			array( 'echo_notification' ),
			array( 'notification_read_timestamp', 'notification_bundle_display_hash' ),
			array(
				'notification_user' => $user->getId(),
				'notification_bundle_hash' => $bundleHash
			),
			__METHOD__,
			array( 'ORDER BY' => 'notification_timestamp DESC', 'LIMIT' => 1 )
		);
		return $res;
	}

	/**
	 * @param $row array
	 * @return int
	 */
	public function createEvent( $row ) {
		$dbw = MWEchoDbFactory::getDB( DB_MASTER );

		$id = $dbw->nextSequenceValue( 'echo_event_id' );

		if ( $id ) {
			$row['event_id'] = $id;
		}

		$dbw->insert( 'echo_event', $row, __METHOD__ );

		if ( !$id ) {
			$id = $dbw->insertId();
		}

		return $id;
	}

	/**
	 * @param $id int
	 * @param $fromMaster bool
	 * @return ResultWrapper
	 * @throws MWException
	 */
	public function loadEvent( $id, $fromMaster = false ) {
		$db = $fromMaster ? MWEchoDbFactory::getDB( DB_MASTER ) : MWEchoDbFactory::getDB( DB_SLAVE );

		$row = $db->selectRow( 'echo_event', '*', array( 'event_id' => $id ), __METHOD__ );

		if ( !$row && !$fromMaster ) {
			return $this->loadEvent( $id, true );
		} elseif ( !$row ) {
			throw new MWException( "No EchoEvent found with ID: $id" );
		}

		return $row;
	}

	/**
	 * @param $event EchoEvent
	 */
	public function updateEventExtra( $event ) {
		$dbw = MWEchoDbFactory::getDB( DB_MASTER );

		$dbw->update(
			'echo_event',
			array( 'event_extra' => $event->serializeExtra() ),
			array( 'event_id' => $event->getId() ),
			__METHOD__
		);
	}

	/**
	 * @param $user User
	 * @param $eventIDs array
	 */
	public function markRead( $user, $eventIDs ) {
		if ( !$eventIDs ) {
			return;
		}

		$dbw = MWEchoDbFactory::getDB( DB_MASTER );

		$dbw->update(
			'echo_notification',
			array( 'notification_read_timestamp' => $dbw->timestamp( wfTimestampNow() ) ),
			array(
				'notification_user' => $user->getId(),
				'notification_event' => $eventIDs,
				'notification_read_timestamp' => null,
			),
			__METHOD__
		);
	}

	/**
	 * @param $user User
	 */
	public function markAllRead( $user ) {
		$dbw = MWEchoDbFactory::getDB( DB_MASTER );

		$dbw->update(
			'echo_notification',
			array( 'notification_read_timestamp' => $dbw->timestamp( wfTimestampNow() ) ),
			array(
				'notification_user' => $user->getId(),
				'notification_read_timestamp' => NULL,
				'notification_bundle_base' => 1,
			),
			__METHOD__
		);
	}

	/**
	 * @param $user User object to check notifications for
	 * @param $dbSource string use master or slave storage to pull count
	 * @return int
	 */
	public function getNotificationCount( $user, $dbSource ) {
		// double check
		if ( !in_array( $dbSource, array( DB_SLAVE, DB_MASTER ) ) ) {
			$dbSource = DB_SLAVE;
		}

		$eventTypesToLoad = EchoNotificationController::getUserEnabledEvents( $user, 'web' );

		if ( !$eventTypesToLoad ) {
			return 0;
		}

		global $wgEchoMaxNotificationCount;

		$db = MWEchoDbFactory::getDB( $dbSource );
		$res = $db->select(
			array( 'echo_notification', 'echo_event' ),
			array( 'notification_event' ),
			array(
				'notification_user' => $user->getId(),
				'notification_bundle_base' => 1,
				'notification_read_timestamp' => null,
				'event_type' => $eventTypesToLoad,
			),
			__METHOD__,
			array( 'LIMIT' => $wgEchoMaxNotificationCount + 1 ),
			array(
				'echo_event' => array( 'LEFT JOIN', 'notification_event=event_id' ),
			)
		);
		return $db->numRows( $res );
	}

	/**
	 * IMPORTANT: should only call this function if the number of unread notification
	 * is reasonable, for example, unread notification count is less than the max
	 * display defined in $wgEchoMaxNotificationCount
	 * @param $user User
	 * @param $type string
	 * @return array
	 */
	public function getUnreadNotifications( $user, $type ) {
		$dbr = MWEchoDbFactory::getDB( DB_SLAVE );
		$res = $dbr->select(
			array( 'echo_notification', 'echo_event' ),
			array( 'notification_event' ),
			array(
				'notification_user' => $user->getId(),
				'notification_bundle_base' => 1,
				'notification_read_timestamp' => null,
				'event_type' => $type,
				'notification_event = event_id'
			),
			__METHOD__
		);

		$eventIds = array();
		foreach ( $res as $row ) {
			$eventIds[$row->notification_event] = $row->notification_event;
		}

		return $eventIds;
	}

}
