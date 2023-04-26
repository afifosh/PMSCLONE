<?php

namespace App\DataTables;

use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Jenssegers\Agent\Agent;

class AuthenticationLogsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->addColumn('ip_address', function ($row) {
                return $row->ip_address;
            })
            ->addColumn('user_agent', function ($row) {
                // $agent = new Agent;
                // $agent->setUserAgent($row->user_agent);
                // return $agent->platform() . ' ' . $agent->version($agent->platform()). ' - ' . $agent->browser();


                
						$device = $this->userAgentDetails($row->user_agent);
						$return = '
						<strong class="p-2 strong" title="' . $row->header . '">
							<i title="' . $device['os'] . '" class="' . $device['p_icon'] . '"></i> 
                            <strong>' . $device['browser'] . ' on ' . $device['os'] . '</strong>
						</strong>';
						return $return;

                // return $row->user_agent;
                //     $agent = tap(new Agent, fn($agent) => $agent->setUserAgent($value));
                //     return $agent->platform() . ' - ' . $agent->browser();
                // $row->user_agent->searchable()
                // ->format(function($value) {
                //     $agent = tap(new Agent, fn($agent) => $agent->setUserAgent($value));
                //     return $agent->platform() . ' - ' . $agent->browser();
                // });

            })
            ->addColumn('login_at', function ($row) {
                return $row->login_at ? $row->login_at->diffForHumans() : '-';
            })
            ->addColumn('login_successful', function ($row) {
                return view('admin.pages.account.login-status', compact('row'));
            })
            ->addColumn('location', function ($row) {
                return view('admin.pages.account.auth-location', compact('row'));
            })

            ->rawColumns(['ip_address', 'user_agent', 'login_at', 'login_successful', 'location']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AuthenticationLog $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AuthenticationLog $model): QueryBuilder
    {
        $user = auth()->user();
        $current_guard = Auth::getDefaultDriver();
        if ($current_guard == "web") {
            $notifiable_type = User::class;
        } elseif ($current_guard == "admin") {
            $notifiable_type = Admin::class;
        }
        return $model
            ->where('authenticatable_id', $user->id)
            ->where('authenticatable_type', $notifiable_type)
            ->orderBy('id', 'DESC')->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('authenticationlogs-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('ip_address'),
            Column::make('user_agent'),
            Column::make('login_at'),
            Column::make('login_successful'),
            Column::make('location'),

        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'AuthenticationLogs_' . date('YmdHis');
    }



	/**
	 * Get User Device Details
	 * @param string|null $ua
	 * @return array
	 */

     public function userAgentDetails($ua = null): array {

		// $ua = is_null($ua) ? self::getHeader() : $ua;
        // $ua = is_null($ua) ? self::getHeader() : $ua;
		$browser = '';
		$platform = '';
		$bIcon = 'ti ti-circle-x';
		$pIcon = 'ti ti-circle-x';

		$browserList = [
			'Trident\/7.0'          => ['Internet Explorer 11','ti ti-browser'],
			'MSIE'                  => ['Internet Explorer','ti ti-browser'],
			'Edge'                  => ['Microsoft Edge','ti ti-brand-edge'],
			'Edg'                   => ['Microsoft Edge','ti ti-brand-edge'],
			'Internet Explorer'     => ['Internet Explorer','ti ti-browser'],
			'Beamrise'              => ['Beamrise','ti ti-planet'],
			'Opera'                 => ['Opera','ti ti-brand-opera'],
			'OPR'                   => ['Opera','ti ti-brand-opera'],
			'Vivaldi'               => ['Vivaldi','ti ti-planet'],
			'Shiira'                => ['Shiira','ti ti-planet'],
			'Chimera'               => ['Chimera','ti ti-planet'],
			'Phoenix'               => ['Phoenix','ti ti-planet'],
			'Firebird'              => ['Firebird','ti ti-planet'],
			'Camino'                => ['Camino','ti ti-planet'],
			'Netscape'              => ['Netscape','ti ti-planet'],
			'OmniWeb'               => ['OmniWeb','ti ti-planet'],
			'Konqueror'             => ['Konqueror','ti ti-planet'],
			'icab'                  => ['iCab','ti ti-planet'],
			'Lynx'                  => ['Lynx','ti ti-planet'],
			'Links'                 => ['Links','ti ti-planet'],
			'hotjava'               => ['HotJava','ti ti-planet'],
			'amaya'                 => ['Amaya','ti ti-planet'],
			'MiuiBrowser'           => ['MIUI Browser','ti ti-planet'],
			'IBrowse'               => ['IBrowse','ti ti-planet'],
			'iTunes'                => ['iTunes','ti ti-planet'],
			'Silk'                  => ['Silk','ti ti-planet'],
			'Dillo'                 => ['Dillo','ti ti-planet'],
			'Maxthon'               => ['Maxthon','ti ti-planet'],
			'Arora'                 => ['Arora','ti ti-planet'],
			'Galeon'                => ['Galeon','ti ti-planet'],
			'Iceape'                => ['Iceape','ti ti-planet'],
			'Iceweasel'             => ['Iceweasel','ti ti-planet'],
			'Midori'                => ['Midori','ti ti-planet'],
			'QupZilla'              => ['QupZilla','ti ti-planet'],
			'Namoroka'              => ['Namoroka','ti ti-planet'],
			'NetSurf'               => ['NetSurf','ti ti-planet'],
			'BOLT'                  => ['BOLT','ti ti-planet'],
			'EudoraWeb'             => ['EudoraWeb','ti ti-planet'],
			'shadowfox'             => ['ShadowFox','ti ti-planet'],
			'Swiftfox'              => ['Swiftfox','ti ti-planet'],
			'Uzbl'                  => ['Uzbl','ti ti-planet'],
			'UCBrowser'             => ['UCBrowser','ti ti-planet'],
			'Kindle'                => ['Kindle','ti ti-planet'],
			'wOSBrowser'            => ['wOSBrowser','ti ti-planet'],
			'Epiphany'              => ['Epiphany','ti ti-planet'],
			'SeaMonkey'             => ['SeaMonkey','ti ti-planet'],
			'Avant Browser'         => ['Avant Browser','ti ti-planet'],
			'Chrome'                => ['Google Chrome','ti ti-brand-chrome'],
			'CriOS'                 => ['Google Chrome','ti ti-brand-chrome'],
			'Safari'                => ['Safari','ti ti-brand-safari'],
			'Firefox'               => ['Firefox','ti ti-brand-firefox'],
			'Mozilla'               => ['Mozilla','ti ti-brand-firefox']
		];

		$platformList = [
			'windows'               => ['Windows','ti ti-brand-windows'],
			'iPad'                  => ['iPad','ti ti-brand-apple'],
			'iPod'                  => ['iPod','ti ti-brand-apple'],
			'iPhone'                => ['iPhone','ti ti-brand-apple'],
			'mac'                   => ['Apple MacOS','ti ti-brand-apple'],
			'android'               => ['Android','ti ti-brand-android'],
			'linux'                 => ['Linux','ti ti-brand-open-source'],
			'Nokia'                 => ['Nokia','ti ti-brand-windows'],
			'BlackBerry'            => ['BlackBerry','ti ti-brand-open-source'],
			'FreeBSD'               => ['FreeBSD','ti ti-brand-open-source'],
			'OpenBSD'               => ['OpenBSD','ti ti-brand-open-source'],
			'NetBSD'                => ['NetBSD','ti ti-brand-open-source'],
			'UNIX'                  => ['UNIX','ti ti-brand-open-source'],
			'DragonFly'             => ['DragonFlyBSD','ti ti-brand-open-source'],
			'OpenSolaris'           => ['OpenSolaris','ti ti-brand-open-source'],
			'SunOS'                 => ['SunOS','ti ti-brand-open-source'],
			'OS\/2'                 => ['OS/2','ti ti-brand-open-source'],
			'BeOS'                  => ['BeOS','ti ti-brand-open-source'],
			'win'                   => ['Windows','ti ti-brand-windows'],
			'Dillo'                 => ['Linux','ti ti-brand-open-source'],
			'PalmOS'                => ['PalmOS','ti ti-brand-open-source'],
			'RebelMouse'            => ['RebelMouse','ti ti-brand-open-source']
		];

		foreach($browserList as $pattern => $name) {
			if ( preg_match("/".$pattern."/i",$ua, $match)) {
				$bIcon = $name[1];
				$browser = $name[0];
				$known = ['Version', $pattern, 'other'];
				$patternVersion = '#(?<browser>' . join('|', $known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
				preg_match_all($patternVersion, $ua, $matches);
				$i = count($matches['browser']);
				if ($i != 1) {
					if (strripos($ua,"Version") < strripos($ua,$pattern)){
						$version = @$matches['version'][0];
					}
					else {
						$version = @$matches['version'][1];
					}
				}
				else {
					$version = @$matches['version'][0];
				}
				break;
			}
		}

		foreach($platformList as $key => $platform) {
			if (stripos($ua, $key) !== false) {
				$pIcon = $platform[1];
				$platform = $platform[0];
				break;
			}
		}

		$browser = $browser == '' ? self::lang('undetected') : $browser;
		$platform = $platform == '' ? self::lang('undetected') : $platform;

		$osPatterns = [
			'/windows nt 10/i'      =>  'Windows 10',
			'/windows nt 6.3/i'     =>  'Windows 8.1',
			'/windows nt 6.2/i'     =>  'Windows 8',
			'/windows nt 6.1/i'     =>  'Windows 7',
			'/windows nt 6.0/i'     =>  'Windows Vista',
			'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'     =>  'Windows XP',
			'/windows xp/i'         =>  'Windows XP',
			'/windows nt 5.0/i'     =>  'Windows 2000',
			'/windows me/i'         =>  'Windows ME',
			'/win98/i'              =>  'Windows 98',
			'/win95/i'              =>  'Windows 95',
			'/win16/i'              =>  'Windows 3.11',
			'/macintosh|mac os x/i' =>  'Mac OS X',
			'/mac_powerpc/i'        =>  'Mac OS 9',
			'/linux/i'              =>  'Linux',
			'/ubuntu/i'             =>  'Ubuntu',
			'/iphone/i'             =>  'iPhone',
			'/ipod/i'               =>  'iPod',
			'/ipad/i'               =>  'iPad',
			'/android/i'            =>  'Android',
			'/blackberry/i'         =>  'BlackBerry',
			'/webos/i'              =>  'Mobile'
		];

		foreach ($osPatterns as $regex => $value) {
			if (preg_match($regex, $ua))
			{
				$osPlatform = $value;
			}
		}

		$version = empty($version) ? '' : 'v'.$version;
		$osPlatform = isset($osPlatform) === false ? self::lang('undetected') : $osPlatform;

		return [
			'user_agent'=> $ua,         // User Agent
			'browser'   => $browser,    // Browser Name
			'version'   => $version,    // Version
			'platform'  => $platform,   // Platform
			'os'        => $osPlatform, // Platform Detail
			'b_icon'    => $bIcon,      // Browser Icon(icon class name like from Material Design Icon)
			'p_icon'    => $pIcon       // Platform Icon(icon class name like from Material Design Icon)
		];

	}

}
