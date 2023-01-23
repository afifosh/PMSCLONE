@isset($pageConfigs)
{!! Helper::updateAdminPageConfig($pageConfigs) !!}
@endisset
@php
$configData = Helper::appAdminClasses();
@endphp

@isset($configData["layout"])
@include((( $configData["layout"] === 'horizontal') ? 'admin.layouts.horizontalLayout' :
(( $configData["layout"] === 'blank') ? 'admin.layouts.blankLayout' : 'admin.layouts.contentNavbarLayout') ))
@endisset
