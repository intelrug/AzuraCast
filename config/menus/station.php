<?php
/**
 * Administrative dashboard configuration.
 */

use App\Acl;

return function (App\Event\BuildStationMenu $e) {
    $request = $e->getRequest();
    $station = $e->getStation();

    $router = $request->getRouter();
    $backend = $request->getStationBackend();
    $frontend = $request->getStationFrontend();

    $settings = $e->getSettings();

    $e->merge(
        [
            'start_station' => [
                'label' => __('Start Station'),
                'title' => __('Ready to start broadcasting? Click to start your station.'),
                'icon' => 'refresh',
                'url' => $router->fromHere('api:stations:restart'),
                'class' => 'api-call text-success',
                'confirm' => __('Restart broadcasting? This will disconnect any current listeners.'),
                'visible' => !$station->getHasStarted(),
                'permission' => Acl::STATION_BROADCASTING,
            ],
            'restart_station' => [
                'label' => __('Restart to Apply Changes'),
                'title' => __('Click to restart your station and apply configuration changes.'),
                'icon' => 'refresh',
                'url' => $router->fromHere('api:stations:restart'),
                'class' => 'api-call text-warning',
                'confirm' => __('Restart broadcasting? This will disconnect any current listeners.'),
                'visible' => $station->getHasStarted() && $station->getNeedsRestart(),
                'permission' => Acl::STATION_BROADCASTING,
            ],
            'profile' => [
                'label' => __('Profile'),
                'icon' => 'image',
                'url' => $router->fromHere('stations:profile:index'),
            ],
            'ondemand' => [
                'label' => __('On-Demand Media'),
                'icon' => 'cloud_download',
                'url' => $router->named('public:ondemand', ['station_id' => $station->getShortName()]),
                'external' => true,
                'visible' => $station->getEnableOnDemand(),
            ],
            'files' => [
                'label' => __('Music Files'),
                'icon' => 'library_music',
                'url' => $router->fromHere('stations:files:index'),
                'visible' => $backend->supportsMedia(),
                'permission' => Acl::STATION_MEDIA,
            ],
            'playlists' => [
                'label' => __('Playlists'),
                'icon' => 'queue_music',
                'url' => $router->fromHere('stations:playlists:index'),
                'visible' => $backend->supportsMedia(),
                'permission' => Acl::STATION_MEDIA,
            ],
            'listeners' => [
                'label' => __('Listeners'),
                'icon' => 'people',
                'url' => $router->fromHere('stations:listeners'),
                'visible' => $frontend->supportsListenerDetail(),
                'permission' => Acl::STATION_REPORTS,
            ],
            'streamers' => [
                'label' => __('Streamer/DJ Accounts'),
                'icon' => 'mic',
                'url' => $router->fromHere('stations:streamers:index'),
                'visible' => $backend->supportsStreamers(),
                'permission' => Acl::STATION_STREAMERS,
            ],
            'requests' => [
                'label' => __('Song Requests'),
                'icon' => 'music_note',
                'url' => $router->fromHere('stations:requests'),
                'visible' => $station->getEnableRequests(),
                'permission' => Acl::STATION_REPORTS,
            ],
            'queue' => [
                'label' => __('Upcoming Song Queue'),
                'icon' => 'queue_play_next',
                'url' => $router->fromHere('stations:queue:index'),
                'visible' => true,
                'permission' => Acl::STATION_BROADCASTING,
            ],
            'web_dj' => [
                'label' => __('Web DJ'),
                'icon' => 'surround_sound',
                'url' => $router->named('public:dj', ['station_id' => $station->getShortName()], [], true)
                    ->withScheme('https'),
                'visible' => false && $station->getEnablePublicPage() && $station->getEnableStreamers(),
                'external' => true,
            ],
            'remotes' => [
                'label' => __('Remote Relays'),
                'icon' => 'router',
                'url' => $router->fromHere('stations:remotes:index'),
                'visible' => false,
                'permission' => Acl::STATION_REMOTES,
            ],
            'reports' => [
                'label' => __('Reports'),
                'icon' => 'assignment',
                'permission' => Acl::STATION_REPORTS,
                'items' => [
                    'reports_overview' => [
                        'label' => __('Statistics Overview'),
                        'url' => $router->fromHere('stations:reports:overview'),
                    ],
                    'reports_timeline' => [
                        'label' => __('Song Playback Timeline'),
                        'url' => $router->fromHere('stations:reports:timeline'),
                    ],
                    'reports_performance' => [
                        'label' => __('Song Listener Impact'),
                        'url' => $router->fromHere('stations:reports:performance'),
                        'visible' => $backend->supportsMedia(),
                    ],
                    'reports_duplicates' => [
                        'label' => __('Duplicate Songs'),
                        'url' => $router->fromHere('stations:files:index') . '#special:duplicates',
                        'visible' => $backend->supportsMedia(),
                    ],
                    'reports_unprocessable' => [
                        'label' => __('Unprocessable Files'),
                        'url' => $router->fromHere('stations:files:index') . '#special:unprocessable',
                        'visible' => $backend->supportsMedia(),
                    ],
                    'reports_soundexchange' => [
                        'label' => __('SoundExchange Royalties'),
                        'url' => $router->fromHere('stations:reports:soundexchange'),
                        'visible' => $frontend->supportsListenerDetail(),
                    ],
                    'public' => [
                        'label' => __('Public Page'),
                        'icon' => 'public',
                        'url' => $router->named('public:index', ['station_id' => $station->getShortName()]),
                        'external' => true,
                        'visible' => $station->getEnablePublicPage(),
                    ],
                ],
            ],
            'utilities' => [
                'label' => __('Utilities'),
                'icon' => 'settings',
                'items' => [
                    'sftp_users' => [
                        'label' => __('SFTP Users'),
                        'url' => $router->fromHere('stations:sftp_users:index'),
                        'visible' => App\Service\SftpGo::isSupportedForStation($station),
                        'permission' => Acl::STATION_MEDIA,
                    ],
                    'automation' => [
                        'label' => __('Automated Assignment'),
                        'url' => $router->fromHere('stations:automation:index'),
                        'visible' => $backend->supportsMedia(),
                        'permission' => Acl::STATION_AUTOMATION,
                    ],
                    'ls_config' => [
                        'label' => __('Edit Liquidsoap Configuration'),
                        'url' => $router->fromHere('stations:util:ls_config'),
                        'visible' => $settings->getEnableAdvancedFeatures()
                            && $backend instanceof App\Radio\Backend\Liquidsoap,
                        'permission' => Acl::STATION_BROADCASTING,
                    ],
                    'logs' => [
                        'label' => __('Log Viewer'),
                        'url' => $router->fromHere('stations:logs:index'),
                        'permission' => Acl::STATION_LOGS,
                    ],
                    'queue' => [
                        'label' => __('Upcoming Song Queue'),
                        'url' => $router->fromHere('stations:queue:index'),
                        'permission' => Acl::STATION_BROADCASTING,
                    ],
                    'mounts' => [
                        'label' => __('Mount Points'),
                        'icon' => 'wifi_tethering',
                        'url' => $router->fromHere('stations:mounts:index'),
                        'visible' => $frontend->supportsMounts(),
                        'permission' => Acl::STATION_MOUNTS,
                    ],
                    'webhooks' => [
                        'label' => __('Web Hooks'),
                        'icon' => 'code',
                        'url' => $router->fromHere('stations:webhooks:index'),
                        'permission' => Acl::STATION_WEB_HOOKS,
                    ],
                    'restart' => [
                        'label' => __('Restart Broadcasting'),
                        'url' => $router->fromHere('api:stations:restart'),
                        'class' => 'api-call',
                        'confirm' => __('Restart broadcasting? This will disconnect any current listeners.'),
                        'permission' => Acl::STATION_BROADCASTING,
                    ],
                ],
            ],
            'rescan_media' => [
                'label' => __('Rescan Media'),
                'icon' => 'find_in_page',
                'url' => $router->fromHere('admin:debug:sync', ['type' => 'manual']),
                'permission' => Acl::GLOBAL_MEDIA_SYNC,
            ],
        ]
    );
};
