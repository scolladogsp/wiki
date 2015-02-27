<?php
/**
 * This class represents the controller for notifications and includes functions
 * for dealing with notification categories.
 */
class EchoNotificationController {
	static protected $blacklist;
	static protected $userWhitelist;

	/**
	 * Get the enabled events for a user, which excludes user-dismissed events
	 * from the general enabled events
	 * @param $user User
	 * @param $outputFormat string
	 * @return array
	 */
	public static function getUserEnabledEvents( $user, $outputFormat ) {
		global $wgEchoNotifications;
		$eventTypesToLoad = $wgEchoNotifications;
		foreach ( $eventTypesToLoad as $eventType => $eventData ) {
			$category = self::getNotificationCategory( $eventType );
			// Make sure the user is eligible to recieve this type of notification
			if ( !self::getCategoryEligibility( $user, $category ) ) {
				unset( $eventTypesToLoad[$eventType] );
			}
			if ( !$user->getOption( 'echo-subscriptions-' . $outputFormat . '-' . $category ) ) {
				unset( $eventTypesToLoad[$eventType] );
			}
		}
		return array_keys( $eventTypesToLoad );
	}

	/**
	 * See if a user is eligible to recieve a certain type of notification
	 * (based on user groups, not user preferences)
	 *
	 * @param $user User object
	 * @param $notificationType string A notification type defined in $wgEchoNotifications
	 * @return boolean
	 */
	public static function getNotificationEligibility( $user, $notificationType ) {
		$category = self::getNotificationCategory( $notificationType );
		return self::getCategoryEligibility( $user, $category );
	}

	/**
	 * See if a user is eligible to recieve a certain type of notification
	 * (based on user groups, not user preferences)
	 *
	 * @param $user User object
	 * @param $category string A notification category defined in $wgEchoNotificationCategories
	 * @return boolean
	 */
	public static function getCategoryEligibility( $user, $category ) {
		global $wgEchoNotificationCategories;
		$usersGroups = $user->getGroups();
		if ( isset( $wgEchoNotificationCategories[$category]['usergroups'] ) ) {
			$allowedGroups = $wgEchoNotificationCategories[$category]['usergroups'];
			if ( !array_intersect( $usersGroups, $allowedGroups ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get the priority for a specific notification type
	 *
	 * @param $notificationType string A notification type defined in $wgEchoNotifications
	 * @return integer From 1 to 10 (10 is default)
	 */
	public static function getNotificationPriority( $notificationType ) {
		$category = self::getNotificationCategory( $notificationType );
		return self::getCategoryPriority( $category );
	}

	/**
	 * Get the priority for a notification category
	 *
	 * @param $category string A notification category defined in $wgEchoNotificationCategories
	 * @return integer From 1 to 10 (10 is default)
	 */
	public static function getCategoryPriority( $category ) {
		global $wgEchoNotificationCategories;
		if ( isset( $wgEchoNotificationCategories[$category]['priority'] ) ) {
			$priority = $wgEchoNotificationCategories[$category]['priority'];
			if ( $priority >= 1 && $priority <= 10 ) {
				return $priority;
			}
		}
		return 10;
	}

	/**
	 * Get the notification category for a notification type
	 *
	 * @param $notificationType string A notification type defined in $wgEchoNotifications
	 * @return String The name of the notification category or 'other' if no
	 *     category is explicitly assigned.
	 */
	public static function getNotificationCategory( $notificationType ) {
		global $wgEchoNotifications, $wgEchoNotificationCategories;
		if ( isset( $wgEchoNotifications[$notificationType]['category'] ) ) {
			$category = $wgEchoNotifications[$notificationType]['category'];
			if ( isset( $wgEchoNotificationCategories[$category] ) ) {
				return $category;
			}
		}
		return 'other';
	}

	/**
	 * Format the notification count with Language::formatNum().  In addition, for large count,
	 * return abbreviated version, e.g. 99+
	 * @param $count int
	 * @return string - formatted number
	 */
	public static function formatNotificationCount( $count ) {
		global $wgLang, $wgEchoMaxNotificationCount;

		if ( $count > $wgEchoMaxNotificationCount ) {
			$count = wfMessage(
				'echo-notification-count',
				$wgLang->formatNum( $wgEchoMaxNotificationCount )
			)->escaped();
		} else {
			$count = $wgLang->formatNum( $count );
		}

		return $count;
	}

	/**
	 * Processes notifications for a newly-created EchoEvent
	 *
	 * @param $event EchoEvent to do notifications for
	 * @param $defer bool Defer to job queue
	 */
	public static function notify( $event, $defer = true ) {
		if ( $defer ) {
			// defer job insertion till end of request when all primary db transactions
			// have been committed
			DeferredUpdates::addCallableUpdate(
				function() use ( $event ) {
					global $wgEchoCluster;
					$params = array( 'event' => $event );
					if ( wfGetLB()->getServerCount() > 1 ) {
						$params['mainDbMasterPos'] = wfGetLB()->getMasterPos();
					}
					if ( $wgEchoCluster ) {
						$lb = wfGetLBFactory()->getExternalLB( $wgEchoCluster );
						if ( $lb->getServerCount() > 1 ) {
							$params['echoDbMasterPos'] = $lb->getMasterPos();
						}
					}

					$title = $event->getTitle() ? $event->getTitle() : Title::newMainPage();
					$job = new EchoNotificationJob( $title, $params );
					JobQueueGroup::singleton()->push( $job );
				}
			);
			return;
		}

		// Check if the event object has valid event type.  Events with invalid
		// event types left in the job queue should not be processed
		if ( !$event->isEnabledEvent() ) {
			return;
		}

		// Only send web notification for welcome event
		if ( $event->getType() == 'welcome' ) {
			self::doNotification( $event, $event->getAgent(), 'web' );
		} else {
			// Get the notification types for this event, eg, web/email
			global $wgEchoDefaultNotificationTypes;
			$notifyTypes = $wgEchoDefaultNotificationTypes['all'];
			if ( isset( $wgEchoDefaultNotificationTypes[$event->getType()] ) ) {
				$notifyTypes = array_merge( $notifyTypes, $wgEchoDefaultNotificationTypes[$event->getType()] );
			}
			$notifyTypes = array_keys( array_filter( $notifyTypes ) );

			$users = self::getUsersToNotifyForEvent( $event );

			$blacklisted = self::isBlacklisted( $event );
			foreach ( $users as $user ) {
				// Notification should not be sent to anonymous user
				if ( $user->isAnon() ) {
					continue;
				}
				if ( $blacklisted && !self::isWhitelistedByUser( $event, $user ) ) {
					continue;
				}

				wfRunHooks( 'EchoGetNotificationTypes', array( $user, $event, &$notifyTypes ) );

				foreach ( $notifyTypes as $type ) {
					self::doNotification( $event, $user, $type );
				}
			}
		}
	}

	/**
	 * Implements blacklist per active wiki expected to be initialized
	 * from InitializeSettings.php
	 *
	 * @param $event EchoEvent The event to test for exclusion via global blacklist
	 * @return boolean True when the event agent is in the global blacklist
	 */
	protected static function isBlacklisted( EchoEvent $event ) {
		if ( !$event->getAgent() ) {
			return false;
		}

		if ( self::$blacklist === null ) {
			global $wgEchoAgentBlacklist, $wgEchoOnWikiBlacklist,
			       $wgMemc;

			self::$blacklist = new EchoContainmentSet;
			self::$blacklist->addArray( $wgEchoAgentBlacklist );
			if ( $wgEchoOnWikiBlacklist !== null ) {
				self::$blacklist->addOnWiki(
					NS_MEDIAWIKI,
					$wgEchoOnWikiBlacklist,
					$wgMemc,
					wfMemcKey( "echo_on_wiki_blacklist")
				);
			}
		}

		return self::$blacklist->contains( $event->getAgent()->getName() );
	}

	/**
	 * Implements per-user whitelist sourced from a user wiki page
	 *
	 * @param $event EchoEvent The event to test for inclusion in whitelist
	 * @param $user User The user that owns the whitelist
	 * @return boolean True when the event agent is in the user whitelist
	 */
	protected static function isWhitelistedByUser( EchoEvent $event, User $user ) {
		global $wgEchoPerUserWhitelistFormat, $wgMemc;


		if ( $wgEchoPerUserWhitelistFormat === null || !$event->getAgent() ) {
			return false;
		}

		$userId = $user->getID();
		if ( $userId === 0 ) {
			return false; // anonymous user
		}

		if ( !isset( self::$userWhitelist[$userId] ) ) {
			self::$userWhitelist[$userId] = new EchoContainmentSet;
			self::$userWhitelist[$userId]->addOnWiki(
				NS_USER,
				sprintf( $wgEchoPerUserWhitelistFormat, $user->getName() ),
				$wgMemc,
				wfMemcKey( "echo_on_wiki_whitelist_" . $userId )
			);
		}

		return self::$userWhitelist[$userId]
			->contains( $event->getAgent()->getName() );
	}

	/**
	 * Processes a single notification for an EchoEvent
	 *
	 * @param $event EchoEvent to do a notification for.
	 * @param $user User object to notify.
	 * @param $type string The type of notification delivery to process, e.g. 'email'.
	 * @throws MWException
	 */
	public static function doNotification( $event, $user, $type ) {
		global $wgEchoNotifiers;

		if ( !isset( $wgEchoNotifiers[$type] ) ) {
			throw new MWException( "Invalid notification type $type" );
		}

		// Don't send any notification if Echo is disabled
		if ( EchoHooks::isEchoDisabled( $user ) ) {
			return;
		}

		call_user_func_array( $wgEchoNotifiers[$type], array( $user, $event ) );
	}

	/**
	 * Retrieves an array of User objects to be notified for an EchoEvent.
	 *
	 * @param $event EchoEvent to retrieve users to be notified for.
	 * @return Array of User objects
	 */
	protected static function getUsersToNotifyForEvent( $event ) {
		$users = $notifyList = array();
		wfRunHooks( 'EchoGetDefaultNotifiedUsers', array( $event, &$users ) );
		// Make sure there is no duplicated users
		foreach ( $users as $user ) {
			$notifyList[$user->getId()] = $user;
		}

		// Don't notify the person who made the edit unless the event extra says to do so
		$extra = $event->getExtra();
		if ( ( !isset( $extra['notifyAgent'] ) || !$extra['notifyAgent'] ) && $event->getAgent() ) {
			unset( $notifyList[$event->getAgent()->getId()] );
		}

		return $notifyList;
	}

	/**
	 * Formats a notification
	 *
	 * @param $event EchoEvent that the notification is for.
	 * @param $user User to format the notification for.
	 * @param $format string The format to show the notification in: text, html, or email
	 * @param $type string The type of notification being distributed (e.g. email, web)
	 * @return string|array The formatted notification, or an array of subject
	 *     and body (for emails), or an error message
	 */
	public static function formatNotification( $event, $user, $format = 'text', $type = 'web' ) {
		global $wgEchoNotifications;

		$eventType = $event->getType();

		if ( isset( $wgEchoNotifications[$eventType] ) ) {
			$params = $wgEchoNotifications[$eventType];
			$notifier = EchoNotificationFormatter::factory( $params );
			$notifier->setOutputFormat( $format );

			return $notifier->format( $event, $user, $type );
		}

		return Xml::tags( 'span', array( 'class' => 'error' ),
			wfMessage( 'echo-error-no-formatter', $event->getType() )->escaped() );
	}
}
