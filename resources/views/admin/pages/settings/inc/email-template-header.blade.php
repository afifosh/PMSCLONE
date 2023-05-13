@php
    $configData = Helper::appClasses();
@endphp

@section('title', __($title))

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{asset('assets/css/app-settings.css')}}" />
    <style>
         #email-editor{
    max-height: 200px;
    overflow: auto;
  }
    </style>
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
    <script src="{{asset('assets/js/helper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-email.js')}}"></script>

    <script src="{{asset('assets/js/custom/app-settings.js')}}"></script>
    <script>

   function submit(elem) {
   var templateId= $('#templateId').val();
   var quill = new Quill('.email-editor');
   $("#html_template").val(quill.root.innerHTML);
    var url="{{url('/admin/settings/email-templates/:templateId')}}";
              url=url.replace(":templateId",templateId);
    saveRecord(elem,"PUT",url,"email-templates-form","Please try again");
    }

   function setActive(template) {
        $('#templateId').val(template.id);
        $('#subject').val(template.subject);
        $('#html_template').val(template.html_template);
        $('.email-editor').html(template.html_template);
        setPlaceHolders(template.placeholders);
    }
    function setPlaceHolders(placeholders){
        var html='';
        placeholders.forEach(element => {
            html+=element.interpolation_start+''+element.tag+''+element.interpolation_end+' - '+ element.description +'</br>';
        });
        $("#placeholders").html(html);
    }
   function placeholderURLConverter(url, node, on_save, name) {
      if (url.indexOf('%7B%7B%20') > -1 && url.indexOf('%20%7D%7D') > -1) {
        url = url.replace('%7B%7B%20', '{').replace('%20%7D%7D', '}')
      }

      return url
    }

   
   function fetch(locale) {
    var url="{{url('/admin/settings/email-templates/:locale/locale')}}";
    url=url.replace(':locale',locale);
    $.ajax({
      url:url,
      method:'GET',
      success:function(response){
        var templates=response.data;
        setActive(templates[0]);  
    }
    })
    }
    setActive({!!json_encode($templates[0])!!});
</script>

@endsection
