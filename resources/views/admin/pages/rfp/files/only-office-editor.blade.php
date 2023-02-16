@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'File Editor')

@section('content')
    @php
        $filetype = 'docx';
        $docKey = getDocEditorKey($file->file);
        $fileuri = FileUri($file->file, true);
        $out = getHistory($file->file, $filetype, $docKey, $fileuri);
        $history = @$out[0];
        $historyData = @$out[1];
        // dd(json_encode($history));
    @endphp
    <div id="placeholder"></div>

    <div>
        <!-- sample modal content -->
        <div id="versionModal" class="modal fade" tabindex="-1" aria-labelledby="versionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="versionModalLabel">Please select version to compare</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @forelse ($historyData as $hisd)
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="version_number"
                                        value="{{ $hisd['url'] }}" @if ($loop->first) checked @endif>
                                    V {{ $hisd['version'] }}
                                </label>
                            </div>
                        @empty
                        @endforelse
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="button"
                            class="btn btn-primary waves-effect waves-light compare-version-btn">Compare</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div> <!-- end preview-->
@endsection

@section('page-script')
    <script type="text/javascript" src="{{ $api_url }}"></script>
    <script>
        $(function() {

        });
        // the document is opened for editing with the old document.key value
        var onOutdatedVersion = function(event) {
            location.reload(true);
        };

        // the user is trying to select document for comparing by clicking the Document from Storage button
        var onRequestCompareFile = function() {
            $('#versionModal').modal('show');
            // let file_url = prompt("enter local url");
            // console.log('pritend');
        };
        $(document).ready(function() {
            $('.compare-version-btn').click(function() {
                var file_url = $('input[name="version_number"]:checked').val();
                var file_data = {
                    'fileType': 'docx',
                    'url': file_url
                }
                console.log(file_data);
                docEditor.setRevisedFile(file_data); // select a document for comparing
                $('#versionModal').modal('hide');
            });
            var config = {
                "token": "{{ $token }}",
                "height": 900,
                "document": {
                    "fileType": "{{$ext}}",
                    "title": "{{ $file->title }}",
                    "url": "{{ $file_url }}",
                    "key": "{{ getDocEditorKey($file->file) }}",
                    "permissions": {
                        "comment": true,
                        "commentGroups": {
                            "edit": "",
                            "remove": "",
                            "view": ""
                        },
                        "copy": true,
                        "deleteCommentAuthorOnly": false,
                        "download": true,
                        "edit": true,
                        "editCommentAuthorOnly": false,
                        "fillForms": true,
                        "modifyContentControl": true,
                        "modifyFilter": true,
                        "print": true,
                        "review": true,
                        "changeHistory": true,
                        "reviewGroups": ["Group1", "Group2", ""]
                    },
                },
                "documentType": "word",
                "editorConfig": {
                    "callbackUrl": "{{ route('update-file', $file) }}",
                    "mode": "edit",
                    "user": {
                        "group": "Group1",
                        "id": "{{ auth()->id() }}",
                        "name": "{{ auth()->user()->full_name }}"
                    },
                    "customization": {
                        "review": {
                            "trackChanges": true
                        }
                    }
                }
            };
            // var сonnectEditor = function() {

            // config = <?php echo json_encode(@$config); ?>;

            config.events = {
                // 'onAppReady': onAppReady,
                // 'onDocumentStateChange': onDocumentStateChange,
                // 'onRequestEditRights': onRequestEditRights,
                // 'onError': onError,
                'onOutdatedVersion': onOutdatedVersion,
                // 'onMakeActionLink': onMakeActionLink,
                // 'onMetaChange': onMetaChange,
                // 'onRequestInsertImage': onRequestInsertImage,
                'onRequestCompareFile': onRequestCompareFile,
                // 'onRequestMailMergeRecipients': onRequestMailMergeRecipients,
            };

            @if ($history != null && $historyData != null)
                window.his = {!! json_encode($history) !!};
                console.log('avail hist', his);
                // the user is trying to show the document version history
                config.events['onRequestHistory'] = function() {
                    docEditor.refreshHistory(
                        his); // show the document version history
                };
                window.histData = {!! json_encode($historyData) !!};
                // the user is trying to click the specific document version in the document version history
                config.events['onRequestHistoryData'] = function(event) {
                    var ver = event.data;
                    console.log('request hist data',event.data);
                    console.log('request hist data data', histData);

                    docEditor.setHistoryData(histData[ver -
                        1]); // send the link to the document for viewing the version history
                };
                config.events['onRequestRestore'] = function(event) {
                    var version = event.data.version;
                    console.log(event.data.version);
                    $.ajax({
                        type: "post",
                        url: "{{route('admin.file.restore_version')}}",
                        data: {
                            version : version,
                            file : '{{$file->file}}'
                        },
                        success: function (response) {
                            // his = response[0];
                            // histData = response[1][version - 5];
                            // docEditor.setHistoryData(response[1][version - 5]);
                            docEditor.refreshHistory(response[0][0]);
                            console.log('respo[0] ',response[0]);
                        }
                    });

                };
                // the user is trying to go back to the document from viewing the document version history
                config.events['onRequestHistoryClose'] = function() {
                    document.location.reload();
                };
            @endif

            console.log(config);


            // docEditor = new DocsAPI.DocEditor("iframeEditor", config);
            // };

            // if (window.addEventListener) {
            //     window.addEventListener("load", сonnectEditor);
            // } else if (window.attachEvent) {
            //     window.attachEvent("load", сonnectEditor);
            // }
            // {{-- env('APP_URL').'/only-office.php?file='.$file->file --}}
            window.docEditor = new DocsAPI.DocEditor("placeholder", config);
        });
    </script>
@endsection
