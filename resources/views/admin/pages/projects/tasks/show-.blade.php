<div class="modal-body">
    <input id="taskid" type="hidden" value="6">
    <div class="row">
        <div class="col-md-8 task-single-col-left" style="min-height: 1006.42px;">
            <div class="task-single-related-wrapper">
                <h4 class="bold font-medium mbot15 tw-mt-0">Related: <a
                        href="https://perfexcrm.com/demo/admin/projects/view/1" target="_blank">#1 - Build Website -
                        Leffler-Kuhic</a>
                    <div class="mtop5 mbot20 font-normal">Milestone: <span
                            class="task-single-menu task-menu-milestones">
                            <span class="trigger pointer manual-popover text-has-action" data-original-title=""
                                title="">
                                Integration Test </span>
                            <span class="content-menu hide">
                                <ul>
                                    <li>
                                        <a href="#" onclick="task_change_milestone(1,6); return false;">
                                            Planning </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="task_change_milestone(2,6); return false;">
                                            Design </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="task_change_milestone(3,6); return false;">
                                            Development </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="task_change_milestone(5,6); return false;">
                                            Production Test </a>
                                    </li>
                                </ul>
                            </span>
                        </span>
                    </div>
                </h4>
            </div>
            <div class="clearfix"></div>
            <p class="no-margin pull-left" style="margin-right:5px !important">
                <a href="#" class="btn btn-primary" id="task-single-mark-complete-btn" autocomplete="off"
                    data-loading-text="Please wait..." onclick="mark_complete(6); return false;" data-toggle="tooltip"
                    title="Mark as complete">
                    <i class="fa fa-check"></i>
                </a>
            </p>
            <p class="no-margin pull-left mright5">
                <a href="#" class="btn btn-default mright5" data-toggle="tooltip" data-title="Timesheets"
                    onclick="slideToggle('#task_single_timesheets'); return false;">
                    <i class="fa fa-th-list"></i>
                </a>
            </p>
            <p class="no-margin pull-left" data-toggle="tooltip"
                data-title="You need to be assigned on this task to start timer!">
                <a href="#" class="mbot10 btn disabled btn-default"
                    onclick="timer_action(this, 6); return false;">
                    <i class="fa-regular fa-clock"></i> Start Timer </a>
            </p>
            <div class="clearfix"></div>
            <hr class="hr-10">
            <div id="task_single_timesheets" class="hide">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="tw-text-sm">Member</th>
                                <th class="tw-text-sm">Start Time</th>
                                <th class="tw-text-sm">End Time</th>
                                <th class="tw-text-sm">Time Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center bold">No started timers found</td>
                            </tr>
                            <tr class="odd">
                                <td colspan="5" class="add-timesheet">
                                    <div class="col-md-12">
                                        <p class="font-medium bold mtop5">Add timesheet</p>
                                        <hr class="mtop10 mbot10">
                                    </div>
                                    <div class="timesheet-start-end-time">
                                        <div class="col-md-6">
                                            <div class="form-group" app-field-wrapper="timesheet_start_time"><label
                                                    for="timesheet_start_time" class="control-label">Start Time</label>
                                                <div class="input-group date"><input type="text"
                                                        id="timesheet_start_time" name="timesheet_start_time"
                                                        class="form-control datetimepicker" value=""
                                                        autocomplete="off">
                                                    <div class="input-group-addon">
                                                        <i class="fa-regular fa-calendar calendar-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" app-field-wrapper="timesheet_end_time"><label
                                                    for="timesheet_end_time" class="control-label">End Time</label>
                                                <div class="input-group date"><input type="text"
                                                        id="timesheet_end_time" name="timesheet_end_time"
                                                        class="form-control datetimepicker" value=""
                                                        autocomplete="off">
                                                    <div class="input-group-addon">
                                                        <i class="fa-regular fa-calendar calendar-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="timesheet-duration hide">
                                        <div class="col-md-12">
                                            <i class="fa-regular fa-circle-question pointer pull-left mtop2"
                                                data-toggle="popover" data-html="true"
                                                data-content="
                                  :15 - 15 Minutes<br ></i>
                                  2 - 2 Hours<br />
                                  5:5 - 5 Hours &amp; 5 Minutes<br />
                                  2:50 - 2 Hours &amp; 50 Minutes<br />
                                  "></i>
                                            <div class="form-group" app-field-wrapper="timesheet_duration"><label
                                                    for="timesheet_duration" class="control-label">Time
                                                    Spent</label><input type="text" id="timesheet_duration"
                                                    name="timesheet_duration" class="form-control"
                                                    placeholder="HH:MM" value=""></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mbot15 mntop15">
                                        <a href="#" class="timesheet-toggle-enter-type">
                                            <span class="timesheet-duration-toggler-text switch-to">
                                                Enter time duration instead </span>
                                            <span class="timesheet-date-toggler-text hide ">
                                                Set start and end time instead </span>
                                        </a>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Member </label>
                                            <br>
                                            <div class="dropdown bootstrap-select bs3" style="width: 100%;"><select
                                                    name="single_timesheet_staff_id" class="selectpicker"
                                                    data-width="100%" tabindex="-98">
                                                    <option value="3">
                                                        Lucious Ziemann </option>
                                                    <option value="2">
                                                        Soledad Hamill </option>
                                                </select><button type="button"
                                                    class="btn dropdown-toggle btn-default" data-toggle="dropdown"
                                                    role="combobox" aria-owns="bs-select-19" aria-haspopup="listbox"
                                                    aria-expanded="false" title="Lucious Ziemann">
                                                    <div class="filter-option">
                                                        <div class="filter-option-inner">
                                                            <div class="filter-option-inner-inner">Lucious Ziemann
                                                            </div>
                                                        </div>
                                                    </div><span class="bs-caret"><span class="caret"></span></span>
                                                </button>
                                                <div class="dropdown-menu open">
                                                    <div class="inner open" role="listbox" id="bs-select-19"
                                                        tabindex="-1">
                                                        <ul class="dropdown-menu inner " role="presentation"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" app-field-wrapper="task_single_timesheet_note"><label
                                                for="task_single_timesheet_note" class="control-label">Note</label>
                                            <textarea id="task_single_timesheet_note" name="task_single_timesheet_note" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <button data-task-id="6" class="btn btn-success task-single-add-timesheet"><i
                                                class="fa fa-plus"></i>
                                            Save</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr>
            </div>
            <div class="clearfix"></div>
            <h4 class="th tw-font-semibold tw-text-base mbot15 pull-left">Description </h4>
            <a href="#" onclick="edit_task_inline_description(this,6); return false;"
                class="pull-left tw-mt-2.5 mleft5 font-medium-xs"><i class="fa-regular fa-pen-to-square"></i></a>
            <div class="clearfix"></div>
            <div class="tc-content">
                <div id="task_view_description">It was the BEST butter,' the March Hare, who had been (Before she had
                    not attended to this mouse?.<br><br>Alice looked round, eager to see the Queen. 'Their heads are
                    gone, if it wasn't trouble enough hatching the eggs,' said the Gryphon interrupted in a trembling
                    voice, '--and I hadn't begun my.</div>
            </div>
            <div class="clearfix"></div>
            <hr>
            <a href="#" onclick="add_task_checklist_item('6', undefined, this); return false"
                class="mbot10 inline-block">
                <span class="new-checklist-item"><i class="fa fa-plus-circle"></i>
                    Checklist Item </span>
            </a>
            <div
                class="form-group no-mbot checklist-templates-wrapper simple-bootstrap-select task-single-checklist-templates">
                <div class="dropdown bootstrap-select checklist-items-template-select bs3" style="width: 100%;">
                    <select id="checklist_items_templates" class="selectpicker checklist-items-template-select"
                        data-none-selected-text="Insert Checklist Templates" data-width="100%"
                        data-live-search="true" tabindex="-98">
                        <option value=""></option>
                        <option value="2">
                            cv </option>
                        <option value="1">
                            fsdfsd </option>
                    </select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder"
                        data-toggle="dropdown" role="combobox" aria-owns="bs-select-20" aria-haspopup="listbox"
                        aria-expanded="false" data-id="checklist_items_templates" title="Insert Checklist Templates">
                        <div class="filter-option">
                            <div class="filter-option-inner">
                                <div class="filter-option-inner-inner">Insert Checklist Templates</div>
                            </div>
                        </div><span class="bs-caret"><span class="caret"></span></span>
                    </button>
                    <div class="dropdown-menu open">
                        <div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off"
                                role="combobox" aria-label="Search" aria-controls="bs-select-20"
                                aria-autocomplete="list"></div>
                        <div class="inner open" role="listbox" id="bs-select-20" tabindex="-1">
                            <ul class="dropdown-menu inner " role="presentation"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <p class="hide text-muted no-margin" id="task-no-checklist-items">
                Checklist items not found for this task</p>
            <div class="row checklist-items-wrapper">
                <div class="col-md-12 ">
                    <div id="checklist-items" class="ui-sortable">
                        <div class="clearfix"></div>
                        <div class="tw-flex tw-justify-between tw-items-center">
                            <h4 class="chk-heading th tw-font-semibold tw-text-base">Checklist Items</h4>
                            <div class="chk-toggle-buttons&quot;">
                                <button class="btn btn-default btn-sm" data-hide="1"
                                    onclick="toggle_completed_checklist_items_visibility(this)">
                                    Hide completed items </button>
                                <button class="btn btn-default btn-sm hide" data-hide="0"
                                    onclick="toggle_completed_checklist_items_visibility(this)">
                                    Show completed items (<span class="task-total-checklist-completed">1</span>)
                                </button>
                            </div>
                        </div>
                        <div class="progress mtop15 no-mbot">
                            <div class="progress-bar not-dynamic progress-bar-default task-progress-bar"
                                role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"
                                style="width: 33.33%;">33.33%</div>
                        </div>
                        <div class="tw-flex tw-flex-col">
                            <div>
                                <div class="checklist ui-sortable-handle" data-checklist-id="13">
                                    <div class="tw-flex">
                                        <div class="checkbox checkbox-success checklist-checkbox"
                                            data-toggle="tooltip" title="">
                                            <input type="checkbox" name="checklist-box" checked="">
                                            <label for=""><span class="hide">Alice aloud, addressing
                                                    nobody.</span></label>
                                        </div>
                                        <div class="tw-grow">
                                            <textarea data-taskid="6" name="checklist-description" rows="1">Alice aloud, addressing nobody.</textarea>
                                        </div>
                                        <div
                                            class="mleft10 tw-inline-flex tw-items-center tw-space-x-1 sm:tw-space-x-2">
                                            <span class="dropdown" data-title="Assign staff" data-toggle="tooltip">
                                                <a href="#" class="tw-text-neutral-500 dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                    id="checklist-item-13" onclick="return false;">
                                                    <i class="fa-regular fa-clock"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-left"
                                                    aria-labelledby="checklist-item-13">
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('1', '13'); return false;">
                                                            Kristian Ziemann </a>
                                                    </li>
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('2', '13'); return false;">
                                                            Soledad Hamill </a>
                                                    </li>
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('3', '13'); return false;">
                                                            Lucious Ziemann </a>
                                                    </li>
                                                </ul>
                                            </span>

                                            <a href="#" class="tw-text-neutral-500 save-checklist-template"
                                                data-toggle="tooltip" data-title="Save as Template"
                                                onclick="save_checklist_item_template(13,this); return false;">
                                                <i class="fa-regular fa-clock"></i>
                                            </a>
                                            <a href="#" class="tw-text-neutral-500 remove-checklist"
                                                onclick="delete_checklist_item(13,this); return false;">
                                                <i class="fa-regular fa-clock"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <p class="font-medium-xs mtop15 tw-text-neutral-500 checklist-item-info">
                                        Created by Lucious Ziemann - Completed by Doug Haley </p>
                                </div>
                            </div>
                            <div>
                                <div class="checklist ui-sortable-handle" data-checklist-id="14">
                                    <div class="tw-flex">
                                        <div class="checkbox checkbox-success checklist-checkbox"
                                            data-toggle="tooltip" title="">
                                            <input type="checkbox" name="checklist-box">
                                            <label for=""><span class="hide">I like being that person, I'll
                                                    come up: if.</span></label>
                                        </div>
                                        <div class="tw-grow">
                                            <textarea data-taskid="6" name="checklist-description" rows="1">I like being that person, I'll come up: if.</textarea>
                                        </div>
                                        <div
                                            class="mleft10 tw-inline-flex tw-items-center tw-space-x-1 sm:tw-space-x-2">
                                            <span class="dropdown" data-title="Assign staff" data-toggle="tooltip">
                                                <a href="#" class="tw-text-neutral-500 dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                    id="checklist-item-14" onclick="return false;">
                                                    <i class="fa-regular fa-clock"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-left"
                                                    aria-labelledby="checklist-item-14">
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('1', '14'); return false;">
                                                            Kristian Ziemann </a>
                                                    </li>
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('2', '14'); return false;">
                                                            Soledad Hamill </a>
                                                    </li>
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('3', '14'); return false;">
                                                            Lucious Ziemann </a>
                                                    </li>
                                                </ul>
                                            </span>

                                            <a href="#" class="tw-text-neutral-500 save-checklist-template"
                                                data-toggle="tooltip" data-title="Save as Template"
                                                onclick="save_checklist_item_template(14,this); return false;">
                                                <i class="fa-regular fa-clock"></i>
                                            </a>
                                            <a href="#" class="tw-text-neutral-500 remove-checklist"
                                                onclick="delete_checklist_item(14,this); return false;">
                                                <i class="fa-regular fa-clock"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="checklist ui-sortable-handle" data-checklist-id="15">
                                    <div class="tw-flex">
                                        <div class="checkbox checkbox-success checklist-checkbox"
                                            data-toggle="tooltip" title="">
                                            <input type="checkbox" name="checklist-box">
                                            <label for=""><span class="hide">Soup! Who cares for
                                                    you?'.</span></label>
                                        </div>
                                        <div class="tw-grow">
                                            <textarea data-taskid="6" name="checklist-description" rows="1">Soup! Who cares for you?'.</textarea>
                                        </div>
                                        <div
                                            class="mleft10 tw-inline-flex tw-items-center tw-space-x-1 sm:tw-space-x-2">
                                            <span class="dropdown" data-title="Assign staff" data-toggle="tooltip">
                                                <a href="#" class="tw-text-neutral-500 dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                    id="checklist-item-15" onclick="return false;">
                                                    <i class="fa-regular fa-clock"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-left"
                                                    aria-labelledby="checklist-item-15">
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('1', '15'); return false;">
                                                            Kristian Ziemann </a>
                                                    </li>
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('2', '15'); return false;">
                                                            Soledad Hamill </a>
                                                    </li>
                                                    <li>
                                                        <a href="#"
                                                            onclick="save_checklist_assigned_staff('3', '15'); return false;">
                                                            Lucious Ziemann </a>
                                                    </li>
                                                </ul>
                                            </span>

                                            <a href="#" class="tw-text-neutral-500 save-checklist-template"
                                                data-toggle="tooltip" data-title="Save as Template"
                                                onclick="save_checklist_item_template(15,this); return false;">
                                                <i class="fa-regular fa-clock"></i>
                                            </a>
                                            <a href="#" class="tw-text-neutral-500 remove-checklist"
                                                onclick="delete_checklist_item(15,this); return false;">
                                                <i class="fa-regular fa-clock"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <p class="font-medium-xs mtop15 tw-text-neutral-500 checklist-item-info">
                                        Created by Soledad Hamill </p>
                                </div>
                            </div>
                        </div>
                        <script>
                            $(function() {
                                $("#checklist-items").sortable({
                                    helper: 'clone',
                                    items: 'div.checklist',
                                    update: function(event, ui) {
                                        update_checklist_order();
                                    }
                                });
                                setTimeout(function() {
                                    do_task_checklist_items_height();
                                }, 200);

                                init_selectpicker();
                                var _hideCompletedItems = ''
                                if (_hideCompletedItems == 1) {
                                    toggle_completed_checklist_items_visibility();
                                }
                            });

                            function toggle_completed_checklist_items_visibility(el, forceShow) {
                                var _task_checklist_items = $("body").find("input[name='checklist-box']");
                                $.each(_task_checklist_items, function() {
                                    var $this = $(this);
                                    if ($this.prop('checked') == true) {
                                        $this.closest('.checklist ').toggleClass('hide');
                                    }
                                });
                                if (typeof el != 'undefined') {
                                    var _hideCompleted = $(el).data('hide');
                                    $(el).addClass('hide');
                                    $(el).siblings().removeClass('hide');
                                    $.post(admin_url + 'staff/save_completed_checklist_visibility', {
                                        task_id: "6",
                                        hideCompleted: _hideCompleted
                                    }, "json");
                                }
                            }

                            function save_checklist_assigned_staff(staffId, list_id) {
                                $.post(
                                    admin_url + 'tasks/save_checklist_assigned_staff', {
                                        assigned: staffId,
                                        checklistId: list_id,
                                        taskId: "6",
                                    }
                                ).done(function(response) {
                                    init_tasks_checklist_items(false, "6");
                                });
                            }
                        </script>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row task_attachments_wrapper">
                <div class="col-md-12" id="attachments">
                    <hr>
                    <h4 class="th tw-font-semibold tw-text-lg mbot15">Attachments</h4>
                    <div class="row">
                        <div data-num="1" data-commentid="39" data-comment-attachment="0"
                            data-task-attachment-id="2" class="task-attachment-col col-md-6">
                            <ul class="list-unstyled task-attachment-wrapper" data-placement="right"
                                data-toggle="tooltip" data-title="readme.txt">
                                <li class="mbot10 task-attachment highlight-bg">
                                    <div class="mbot10 pull-right task-attachment-user">
                                        <a href="#" class="pull-right"
                                            onclick="remove_task_attachment(this,2); return false;">
                                            <i class="fa fa fa-times"></i>
                                        </a>
                                        <a href="https://perfexcrm.com/demo/admin/profile/1" target="_blank">Kristian
                                            Ziemann</a> - <span class="text-has-action tw-text-sm"
                                            data-toggle="tooltip" data-title="2023-07-10 10:51:04">Just now</span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="task-attachment-no-preview">
                                        <a href="https://perfexcrm.com/demo/download/file/taskattachment/720340d4d8b7505799df606e8dc2fac8"
                                            target="_blank" class="">
                                            <i class="mime mime-file"></i>
                                            readme.txt </a>
                                    </div>
                                    <div class="clearfix"></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 text-center">
                    <hr>
                    <a href="https://perfexcrm.com/demo/admin/tasks/download_files/6" class="bold">
                        Download All (.zip)
                    </a>
                </div>
            </div>
            <hr>
            <a href="#" id="taskCommentSlide" onclick="slideToggle('.tasks-comments'); return false;">
                <h4 class="mbot20 font-medium">Comments</h4>
            </a>
            <div class="tasks-comments inline-block full-width simple-editor">
                <form action="https://perfexcrm.com/demo/admin/tasks/add_task_comment" id="task-comment-form"
                    class="dropzone dropzone-manual" style="min-height:auto;background-color:#fff;"
                    enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    <input type="hidden" name="csrf_token_name" value="cf2319b64d75b42b15bdf857db60c75b">
                    <textarea name="comment" placeholder="Add Comment" id="task_comment" rows="3"
                        class="form-control ays-ignore"></textarea>
                    <div id="dropzoneTaskComment"
                        class="dropzoneDragArea dz-default dz-message hide task-comment-dropzone">
                        <span>Drop files here to upload</span>
                    </div>
                    <div class="dropzone-task-comment-previews dropzone-previews"></div>
                    <button type="button" class="btn btn-primary mtop10 pull-right hide" id="addTaskCommentBtn"
                        autocomplete="off" data-loading-text="Please wait..." onclick="add_task_comment('6');"
                        data-comment-task-id="6">
                        Add Comment </button>
                </form>
                <div class="clearfix"></div>
                <hr>
                <div id="task-comments" class="mtop10">
                    <div id="comment_39" data-commentid="39" data-task-attachment-id="2"
                        class="tc-content task-comment highlight-bg"><a data-task-comment-href-id="39"
                            href="https://perfexcrm.com/demo/admin/tasks/view/6#comment_39"
                            class="task-date-as-comment-id"><span class="tw-text-sm"><span
                                    class="text-has-action inline-block" data-toggle="tooltip"
                                    data-title="2023-07-10 10:51:04">Just now</span></span></a><a
                            href="https://perfexcrm.com/demo/admin/profile/1" target="_blank"><img
                                src="https://perfexcrm.com/demo/uploads/staff_profile_images/1/small_1.png"
                                class="staff-profile-image-small media-object img-circle pull-left mright10"></a><span
                            class="pull-right"><a href="#" onclick="remove_task_comment(39); return false;"><i
                                    class="fa fa-times text-danger"></i></a></span><span class="pull-right mright5"><a
                                href="#" onclick="edit_task_comment(39); return false;"><i
                                    class="fa-regular fa-pen-to-square"></i></a></span>
                        <div class="media-body comment-wrapper">
                            <div class="mleft40"><a href="https://perfexcrm.com/demo/admin/profile/1"
                                    target="_blank">Kristian Ziemann</a> <br>
                                <div data-edit-comment="39" class="hide edit-task-comment">
                                    <textarea rows="5" id="task_comment_39" class="ays-ignore form-control"></textarea>
                                    <div class="clearfix mtop20"></div>
                                    <button type="button" class="btn btn-primary pull-right"
                                        onclick="save_edited_comment(39,6)">Save</button>
                                    <button type="button" class="btn btn-default pull-right mright5"
                                        onclick="cancel_edit_comment(39)">Cancel</button>
                                </div>
                                <div class="comment-content mtop10">
                                    <div class="clearfix"></div>
                                    <div data-num="1" data-commentid="39" data-comment-attachment="0"
                                        data-task-attachment-id="2" class="task-attachment-col col-md-6">
                                        <ul class="list-unstyled task-attachment-wrapper" data-placement="right"
                                            data-toggle="tooltip" data-title="readme.txt">
                                            <li class="mbot10 task-attachment highlight-bg">
                                                <div class="mbot10 pull-right task-attachment-user">
                                                    <a href="#" class="pull-right"
                                                        onclick="remove_task_attachment(this,2); return false;">
                                                        <i class="fa fa fa-times"></i>
                                                    </a>
                                                    <a href="https://perfexcrm.com/demo/admin/profile/1"
                                                        target="_blank">Kristian Ziemann</a> - <span
                                                        class="text-has-action tw-text-sm" data-toggle="tooltip"
                                                        data-title="2023-07-10 10:51:04">Just now</span>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="task-attachment-no-preview">
                                                    <a href="https://perfexcrm.com/demo/download/file/taskattachment/720340d4d8b7505799df606e8dc2fac8"
                                                        target="_blank" class="">
                                                        <i class="mime mime-file"></i>
                                                        readme.txt </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <hr class="task-info-separator">
                        </div>
                    </div>
                    <div id="comment_38" data-commentid="38" data-task-attachment-id="0"
                        class="tc-content task-comment highlight-bg"><a data-task-comment-href-id="38"
                            href="https://perfexcrm.com/demo/admin/tasks/view/6#comment_38"
                            class="task-date-as-comment-id"><span class="tw-text-sm"><span
                                    class="text-has-action inline-block" data-toggle="tooltip"
                                    data-title="2023-07-10 10:50:55">Just now</span></span></a><a
                            href="https://perfexcrm.com/demo/admin/profile/1" target="_blank"><img
                                src="https://perfexcrm.com/demo/uploads/staff_profile_images/1/small_1.png"
                                class="staff-profile-image-small media-object img-circle pull-left mright10"></a><span
                            class="pull-right"><a href="#" onclick="remove_task_comment(38); return false;"><i
                                    class="fa fa-times text-danger"></i></a></span><span class="pull-right mright5"><a
                                href="#" onclick="edit_task_comment(38); return false;"><i
                                    class="fa-regular fa-pen-to-square"></i></a></span>
                        <div class="media-body comment-wrapper">
                            <div class="mleft40"><a href="https://perfexcrm.com/demo/admin/profile/1"
                                    target="_blank">Kristian Ziemann</a> <br>
                                <div data-edit-comment="38" class="hide edit-task-comment">
                                    <textarea rows="5" id="task_comment_38" class="ays-ignore form-control">&lt;p&gt;test&lt;/p&gt;</textarea>
                                    <div class="clearfix mtop20"></div>
                                    <button type="button" class="btn btn-primary pull-right"
                                        onclick="save_edited_comment(38,6)">Save</button>
                                    <button type="button" class="btn btn-default pull-right mright5"
                                        onclick="cancel_edit_comment(38)">Cancel</button>
                                </div>
                                <div class="comment-content mtop10">
                                    <p>test</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 task-single-col-right">
            <div class="pull-right mbot10 task-single-menu task-menu-options">
                <div class="content-menu hide">
                </div>
                <a href="#" onclick="return false;" class="trigger manual-popover mright5"
                    data-original-title="" title="">
                    <i class="fa-regular fa-circle"></i>
                    <i class="fa-regular fa-circle"></i>
                    <i class="fa-regular fa-circle"></i>
                </a>
            </div>
            <h4 class="task-info-heading tw-font-medium tw-text-base tw-mb-0 tw-text-neutral-800">
                Task Info </h4>
            <div class="clearfix"></div>
            <p class="tw-mb-0 task-info-created tw-text-sm">
                <span class="tw-text-neutral-500">Created at <span class="tw-text-neutral-600">2023-07-10
                        09:00:13</span></span>
            </p>
            <hr class="task-info-separator">
            <div class="task-info task-status task-info-status">
                <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
                    <i class="fa-regular fa-star fa-fw fa-lg pull-left task-info-icon"></i>Status:
                    <span class="task-single-menu task-menu-status">
                        <span class="trigger pointer manual-popover text-has-action tw-text-neutral-800"
                            data-original-title="" title="">
                            In Progress </span>
                        <span class="content-menu hide">
                        </span>
                    </span>
                </h5>
            </div>
            <div class="task-info task-single-inline-wrap task-info-start-date">
                <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
                    <div class="tw-shrink-0 tw-grow">
                        <i class="fa-regular fa-calendar fa-fw fa-lg fa-margin task-info-icon pull-left tw-mt-2"></i>
                        Start Date:
                    </div>
                    <input name="startdate" tabindex="-1" value="2023-07-10" id="task-single-startdate"
                        class="task-info-inline-input-edit datepicker pointer task-single-inline-field tw-text-neutral-800">
                </h5>
            </div>
            <div class="task-info task-info-due-date task-single-inline-wrap">
                <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
                    <div class="tw-shrink-0 tw-grow">
                        <i class="fa-regular fa-calendar-check fa-fw fa-lg task-info-icon pull-left tw-mt-2"></i>
                        Due Date:
                    </div>
                    <input name="duedate" tabindex="-1" value="2023-07-24" id="task-single-duedate"
                        class="task-info-inline-input-edit datepicker pointer task-single-inline-field tw-text-neutral-800"
                        autocomplete="off" data-date-end-date="2023-09-10">
                </h5>
            </div>
            <div class="task-info task-info-priority">
                <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
                    <i class="fa fa-bolt fa-fw fa-lg task-info-icon pull-left"></i>
                    Priority:
                    <span class="task-single-menu task-menu-priority">
                        <span class="trigger pointer manual-popover text-has-action" style="color:#777;"
                            data-original-title="" title="">
                            Low </span>
                        <span class="content-menu hide">
                            <ul>
                                <li>
                                    <a href="#" onclick="task_change_priority(2,6); return false;"
                                        class="tw-block">
                                        Medium </a>
                                </li>
                                <li>
                                    <a href="#" onclick="task_change_priority(3,6); return false;"
                                        class="tw-block">
                                        High </a>
                                </li>
                                <li>
                                    <a href="#" onclick="task_change_priority(4,6); return false;"
                                        class="tw-block">
                                        Urgent </a>
                                </li>
                            </ul>
                        </span>
                    </span>
                </h5>
            </div>
            <div class="task-info task-info-hourly-rate">
                <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
                    <i class="fa-regular fa-clock fa-fw fa-lg task-info-icon pull-left"></i>
                    Hourly Rate: <span class="tw-text-neutral-800">
                        0.00 </span>
                </h5>
            </div>
            <div class="task-info task-info-billable">
                <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
                    <i class="fa fa-credit-card fa-fw fa-lg task-info-icon pull-left"></i>
                    Billable: <span class="tw-text-neutral-800">
                        Billable <b>(Not Billed)</b>
                    </span>
                </h5>
                <br><span class="tw-ml-5 tw-text-sm">(Project Fixed Rate)</span>
            </div>
            <div class="task-info task-info-total-logged-time">
                <h5 class="tw-inline-flex tw-items-center tw-space-x-1.5">
                    <i class="fa-regular fa-clock fa-fw fa-lg task-info-icon"></i>Total logged time: <span
                        class="text-success">
                        00:00 </span>
                </h5>
            </div>
            <div class="mtop10 clearfix"></div>
            <div id="inputTagsWrapper" class="taskSingleTasks task-info-tags-edit">
                <input type="text" class="tagsinput tagit-hidden-field" id="taskTags" data-taskid="6"
                    value="" data-role="tagsinput">
                <ul class="tagit ui-widget ui-widget-content ui-corner-all">
                    <li class="tagit-new"><input type="text" class="ui-widget-content ui-autocomplete-input"
                            placeholder="Tag" autocomplete="off"></li>
                </ul>
                <ul id="ui-id-4" tabindex="0"
                    class="ui-menu ui-widget ui-widget-content ui-autocomplete ui-front tagit-autocomplete"
                    style="display: none;"></ul>
            </div>
            <div class="clearfix"></div>
            <hr class="task-info-separator">
            <div class="clearfix"></div>
            <h4 class="task-info-heading tw-font-medium tw-text-base tw-flex tw-items-center tw-text-neutral-800">
                <i class="fa-regular fa-clock"></i>
                Reminders
            </h4>
            <a href="#" onclick="new_task_reminder(6); return false;">
                Create Reminder </a>
            <div class="display-block tw-text-neutral-600 tw-text-sm mtop15">
                No reminders for this task </div>
            <div class="clearfix"></div>
            <div id="newTaskReminderToggle" class="mtop15" style="display:none;">
                <form action="https://perfexcrm.com/demo/admin/tasks/upload_file" id="form-reminder-task"
                    method="post" accept-charset="utf-8" novalidate="novalidate">
                    <input type="hidden" name="csrf_token_name" value="cf2319b64d75b42b15bdf857db60c75b">

                    <input type="hidden" name="rel_id" value="6">

                    <input type="hidden" name="rel_type" value="task">
                    <div class="form-group" app-field-wrapper="date"><label for="date" class="control-label">
                            <small class="req text-danger">* </small>Date to be notified</label>
                        <div class="input-group date"><input type="text" id="date" name="date"
                                class="form-control datetimepicker" data-date-min-date="2023-07-10" data-step="30"
                                value="" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="fa-regular fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" app-field-wrapper="staff"><label for="staff" class="control-label">
                            <small class="req text-danger">* </small>Set reminder to</label>
                        <div class="dropdown bootstrap-select bs3" style="width: 100%;"><select id="staff"
                                name="staff" class="selectpicker" data-current-staff="1" data-width="100%"
                                data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98">
                                <option value=""></option>
                                <option value="1" selected="">Kristian Ziemann</option>
                                <option value="2">Soledad Hamill</option>
                                <option value="3">Lucious Ziemann</option>
                            </select><button type="button" class="btn dropdown-toggle btn-default"
                                data-toggle="dropdown" role="combobox" aria-owns="bs-select-21"
                                aria-haspopup="listbox" aria-expanded="false" data-id="staff"
                                title="Kristian Ziemann">
                                <div class="filter-option">
                                    <div class="filter-option-inner">
                                        <div class="filter-option-inner-inner">Kristian Ziemann</div>
                                    </div>
                                </div><span class="bs-caret"><span class="caret"></span></span>
                            </button>
                            <div class="dropdown-menu open">
                                <div class="bs-searchbox"><input type="search" class="form-control"
                                        autocomplete="off" role="combobox" aria-label="Search"
                                        aria-controls="bs-select-21" aria-autocomplete="list"></div>
                                <div class="inner open" role="listbox" id="bs-select-21" tabindex="-1">
                                    <ul class="dropdown-menu inner " role="presentation"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" app-field-wrapper="description"><label for="description"
                            class="control-label"> <small class="req text-danger">* </small>Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="notify_by_email" id="notify_by_email">
                            <label for="notify_by_email">Send also an email for this reminder</label>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm pull-right" type="submit" id="taskReminderFormSubmit">
                        Create Reminder </button>
                    <div class="clearfix"></div>
                </form>
            </div>
            <hr class="task-info-separator">
            <div class="clearfix"></div>
            <h4
                class="task-info-heading tw-font-medium tw-text-base tw-flex tw-items-center tw-text-neutral-800 tw-mb-1">
                <i class="fa-regular fa-clock"></i>
                Assignees
            </h4>
            <div class="simple-bootstrap-select tw-mb-2">
                <div class="dropdown bootstrap-select text-muted task-action-select bs3" style="width: 100%;"><select
                        data-width="100%" data-live-search-placeholder="Search Project Members..." data-task-id="6"
                        id="add_task_assignees" class="text-muted task-action-select selectpicker"
                        name="select-assignees" data-live-search="true" title="Assign task to"
                        data-none-selected-text="Nothing selected" tabindex="-98">
                        <option class="bs-title-option" value=""></option>
                        <option value="1">Kristian Ziemann</option>
                    </select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder"
                        data-toggle="dropdown" role="combobox" aria-owns="bs-select-22" aria-haspopup="listbox"
                        aria-expanded="false" data-id="add_task_assignees" title="Assign task to">
                        <div class="filter-option">
                            <div class="filter-option-inner">
                                <div class="filter-option-inner-inner">Assign task to</div>
                            </div>
                        </div><span class="bs-caret"><span class="caret"></span></span>
                    </button>
                    <div class="dropdown-menu open">
                        <div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off"
                                placeholder="Search Project Members..." role="combobox" aria-label="Search"
                                aria-controls="bs-select-22" aria-autocomplete="list"></div>
                        <div class="inner open" role="listbox" id="bs-select-22" tabindex="-1">
                            <ul class="dropdown-menu inner " role="presentation"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="task_users_wrapper">

                <div class="task-user" data-toggle="tooltip" data-title="Lucious Ziemann">
                    <a href="https://perfexcrm.com/demo/admin/profile/3" target="_blank"><img
                            src="https://perfexcrm.com/demo/assets/images/user-placeholder.jpg"
                            class="staff-profile-image-small"></a> <a href="#"
                        class="remove-task-user text-danger" onclick="remove_assignee(11,6); return false;"><i
                            class="fa fa-remove"></i></a>
                </div>
                <div class="task-user" data-toggle="tooltip" data-title="Soledad Hamill">
                    <a href="https://perfexcrm.com/demo/admin/profile/2" target="_blank"><img
                            src="https://perfexcrm.com/demo/uploads/staff_profile_images/2/small_2.png"
                            class="staff-profile-image-small"></a> <a href="#"
                        class="remove-task-user text-danger" onclick="remove_assignee(10,6); return false;"><i
                            class="fa fa-remove"></i></a>
                </div>
            </div>
            <hr class="task-info-separator">
            <div class="clearfix"></div>
            <h4
                class="task-info-heading tw-font-medium tw-text-base tw-flex tw-items-center tw-text-neutral-800 tw-mb-1">
                <i class="fa-regular fa-clock"></i>
                Followers
            </h4>
            <div class="simple-bootstrap-select tw-mb-2">
                <div class="dropdown bootstrap-select text-muted task-action-select bs3" style="width: 100%;"><select
                        data-width="100%" data-task-id="6" class="text-muted selectpicker task-action-select"
                        name="select-followers" data-live-search="true" title="Add Followers"
                        data-none-selected-text="Nothing selected" tabindex="-98">
                        <option class="bs-title-option" value=""></option>
                        <option value="4">Trystan Murphy</option>
                        <option value="2">Soledad Hamill</option>
                        <option value="9">Paul Gerhold</option>
                        <option value="10">Miguel Kuhic</option>
                        <option value="3">Lucious Ziemann</option>
                        <option value="5">Lorenz Gislason</option>
                        <option value="1">Kristian Ziemann</option>
                        <option value="8">Doug Haley</option>
                        <option value="6">Dameon Fadel</option>
                        <option value="7">Camron O'Connell</option>
                    </select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder"
                        data-toggle="dropdown" role="combobox" aria-owns="bs-select-23" aria-haspopup="listbox"
                        aria-expanded="false" title="Add Followers">
                        <div class="filter-option">
                            <div class="filter-option-inner">
                                <div class="filter-option-inner-inner">Add Followers</div>
                            </div>
                        </div><span class="bs-caret"><span class="caret"></span></span>
                    </button>
                    <div class="dropdown-menu open">
                        <div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off"
                                role="combobox" aria-label="Search" aria-controls="bs-select-23"
                                aria-autocomplete="list"></div>
                        <div class="inner open" role="listbox" id="bs-select-23" tabindex="-1">
                            <ul class="dropdown-menu inner " role="presentation"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="task_users_wrapper">
                <div class="display-block tw-text-neutral-600 mbot5 tw-text-sm">No followers for this task</div>
            </div>
            <form action="https://perfexcrm.com/demo/admin/tasks/upload_file" id="task-attachment"
                class="dropzone tw-mt-5 dz-clickable" enctype="multipart/form-data" method="post"
                accept-charset="utf-8">
                <input type="hidden" name="csrf_token_name" value="cf2319b64d75b42b15bdf857db60c75b">
                <div class="dz-default dz-message"><span>Drop files here to upload</span></div>
            </form>
            <div class="tw-my-2 tw-inline-flex tw-items-end tw-w-full tw-flex-col tw-space-y-2 tw-justify-end">
                <button class="gpicker" style="opacity: 0;">
                    <i class="fa-brands fa-google" aria-hidden="true"></i>
                    Choose from Google Drive </button>
                <div id="dropbox-chooser-task"><a href="#"
                        class=" dropbox-dropin-btn dropbox-dropin-default"><span
                            class="dropin-btn-status"></span>Choose from Dropbox</a></div>
            </div>
        </div>
    </div>
</div>
