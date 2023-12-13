@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Access List')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<style>
  .treeselect .treeselect-list {
    position: relative !important;
  }
  .treeselect-list__item--checked .treeselect-list__item-checkbox-container, .treeselect-list__item--partial-checked .treeselect-list__item-checkbox-container {
   background-color: var(--bs-primary) !important;
  }

  /* check item bg */
  /* .treeselect-list__item--checked {
    background-color: var(--bs-primary) !important;
  } */

  /* on hover of list item */
  /* .treeselect-list .treeselect-list__item--focused {
    background-color: var(--bs-primary) !important;
  } */

  /* tags in input */
  /* .treeselect-input__tags-element {
  background-color: var(--bs-primary) !important;
  color: #fff !important;
  } */
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script src="https://cdn.jsdelivr.net/npm/treeselectjs@0.10.0/dist/treeselectjs.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/treeselectjs@0.10.0/dist/treeselectjs.css" />
<script>
  function initACLCreateTreeSelect(params)
  {
    const domElement = document.querySelector('.acl-create-treeselect')
    const treeselect = new Treeselect({
      parentHtmlContainer: domElement,
      value: params.selected_programs,
      options: params.programs_tree,
      isIndependentNodes: true,
      showCount: true,
      openLevel: 150,
      alwaysOpen: true,
      staticList: true
    })

    $('#acl-create-form .accessible-programs-input').val(params.selected_programs);

    treeselect.srcElement.addEventListener('input', (e) => {
      $('#acl-create-form .accessible-programs-input').val(e.detail);
    })
  }

  function initProgramACLEditTreeSelect(params)
  {
    const domElement = document.querySelector('.acl-create-treeselect')
    const treeselect = new Treeselect({
      parentHtmlContainer: domElement,
      value: params.selected_programs,
      options: params.programs_tree,
      isIndependentNodes: true,
      showCount: true,
      openLevel: 150,
      alwaysOpen: true,
      staticList: true,
    })

    $('#acl-create-form .accessible-programs-input').val(params.selected_programs);

    treeselect.srcElement.addEventListener('input', (e) => {
      $('#acl-create-form .accessible-programs-input').val(e.detail);
    })
  }
</script>
@endsection

@section('content')
<h4 class="fw-semibold mb-4">{{__('Access List')}}</h4>

<div class="mt-3  col-12">
  <div class="card">
    <div class="card-body">
      {{$dataTable->table()}}
    </div>
  </div>
</div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script>
      function revokeContractAccess(admin_id, contract_id, status){
        $.ajax({
          type: "POST",
          url: route('admin.admin-access-lists.contracts.revoke-access', {admin_access_list: admin_id, contract: contract_id}),
          data: {
            is_revoked: status
          },
          success: function (response) {
            if (response.data.event == 'table_reload') {
              if (response.data.table_id != undefined && response.data.table_id != null && response.data.table_id != '') {
                $('#' + response.data.table_id).DataTable().ajax.reload(null, false);
              } else {
                $('#dataTableBuilder').DataTable().ajax.reload(null, false);
              }
            }
          }
        });
      }

      function revokeProgramAccess(admin_id, program_id, status)
      {
        $.ajax({
          type: "POST",
          url: route('admin.admin-access-lists.programs.revoke-access', {admin_access_list: admin_id, program: program_id}),
          data: {
            is_revoked: status
          },
          success: function (response) {
            if (response.data.event == 'table_reload') {
              if (response.data.table_id != undefined && response.data.table_id != null && response.data.table_id != '') {
                $('#' + response.data.table_id).DataTable().ajax.reload(null, false);
              } else {
                $('#dataTableBuilder').DataTable().ajax.reload(null, false);
              }
            }
          }
        });
      }
      $(document).ready(function() {
        // Variable to track the currently expanded row
        var expandedRow = null;
        var table = $('#admin-access-lists-table').DataTable();
        window.renderProgramsTable = function(user_id, element){
          var tr = $(element).closest('tr');
          var selectedRow = table.row(tr);
          // If this row is already shown, hide it
          if (selectedRow.child.isShown()) {
              // Find any added child rows and remove them
              tr.nextAll('.child-row-added').remove();

              selectedRow.child.hide();
              $(tr).removeClass('shown');
              $(tr).css('background-color', '');
          } else {
              // If another row is expanded, collapse it
              if (expandedRow) {
                  expandedRow.child.hide();
                  $(expandedRow.node()).removeClass('shown');
                  $(expandedRow.node()).css('background-color', '');
                  $(expandedRow.node()).nextAll('.child-row-added').remove();
              }

              createProgramsTable(selectedRow, user_id);
              $(tr).addClass('shown');
              $(tr).css('background-color', '#f5f5f5');

              expandedRow = selectedRow; // Update the reference to the currently expanded row
          }
        }
        function createProgramsTable(row, user_id) {
          // Check if child table already exists, if so, destroy it
          var existingChildTable = $('#programs-table-' + user_id).DataTable();
          if (existingChildTable) {
            existingChildTable.destroy();
          }

          if (expandedRow) {
              // Collapse all other rows in the table except the selected row
              table.rows().every(function() {
                  var tr = this.node();
                  if (tr !== row.node()) {
                      var otherRow = table.row(tr);
                      otherRow.child.hide();
                      $(tr).removeClass('shown');
                      $(tr).css('background-color', ''); // Reset the background color
                  }
              });
          }


          // 1st Row - For the pills
          var pills = `
              <tr class="child-row-added">
                  <td colspan="100%">
                      <ul class="nav nav-pills mt-3 mb-3" role="tablist">
                          <li class="nav-item" role="presentation">
                              <button type="button" onClick="$('#programs-child-table-${user_id}').DataTable().ajax.reload(null, false)" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#programs-table-${user_id}" aria-controls="programs-table-${user_id}" aria-selected="false">Programs</button>
                          </li>
                          <li class="nav-item" role="presentation">
                              <button type="button" onClick="$('#child-table-${user_id}').DataTable().ajax.reload(null, false)" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#child-contracts-${user_id}" aria-controls="child-contracts-${user_id}" aria-selected="true">Contracts</button>
                          </li>
                      </ul>
                  </td>
              </tr>`;

          row.child(pills).show();

          // 2nd Row - For the content
          var content = `
              <tr class="child-row-added p-0">
                  <td class="p-0" colspan="100%">
                      <div class="tab-content">
                          <div class="tab-pane fade show active" id="programs-table-${user_id}" role="tabpanel" aria-labelledby="programs-table-tab-${user_id}">
                              <table class="table"  id="programs-child-table-${user_id}"></table>
                          </div>
                          <div class="tab-pane fade" id="child-contracts-${user_id}" role="tabpanel" aria-labelledby="child-contracts-tab-${user_id}">
                              <table class="table" id="child-table-${user_id}"></table>
                          </div>
                      </div>
                  </td>
              </tr>
          `;

          // Insert the content row after the pills row
          // $(row.node()).next().after(content);

          var contentRow = $(content);
          contentRow.addClass("custom-content-row");
          contentRow.css("background-color", "#f9f9f9"); // Example: change the background color
          $(row.node()).next().after(contentRow);
          // Insert the content row after the pills row
          //  $(row.node()).after(content);
          var childTableId = "child-table-" + user_id;

          // Programs DataTable
          $('#programs-child-table-' + user_id).DataTable({
              processing: true,
              serverSide: true,
              ajax: route('admin.admin-access-lists.programs.index', { admin_access_list: user_id }),
              columns: [
                { data: "id", "name": "programs.id", title: 'ID' },
                { data: "name", "name": "programs.name", title: 'Program Name' },
                { data: "granted_till", title: 'Granted Till' },
                { data: "status", title: 'Status' },
                { data: "revoke_access", title: 'Revoke Access', orderable: false, searchable: false},
                { data: "actions", title: 'Actions', orderable: false, searchable: false }
              ],
              buttons : [
                {
                  text: 'Add Program',
                  className: 'btn btn-primary',
                  attr: {
                      'data-toggle': 'ajax-modal',
                      'data-title': 'Add Program',
                      'data-href': route('admin.admin-access-lists.programs.create', { admin_access_list: user_id })
                  }
                }
              ],
              destroy: true,
              dom: 'Blfrtip',
              // Add more DataTable options if required
          });

          $('#' + childTableId).DataTable({
            processing: true,
            serverSide: true,
            ajax: route('admin.admin-access-lists.contracts.index', { admin_access_list: user_id }),
            columns: [
              { data: "id", "name": "contracts.id", title: 'ID' },
              { data: "subject", title: 'subject' },
              { data: "program", title: "Program"},
              { data: "access_type", title: "Type"},
              { data: "granted_till", title: 'Granted Till' },
              { data: "status", title: 'Status' },
              { data: "revoke_access", title: 'Revoke Access', orderable: false, searchable: false},
              { data: "actions", title: 'Actions', orderable: false, searchable: false }
            ],
            destroy: true,
            dom: 'Blfrtip',
            buttons: [
              {
                text: 'Add Contract',
                className: 'btn btn-primary',
                attr: {
                    'data-toggle': 'ajax-modal',
                    'data-title': 'Add Contract Access Rule',
                    'data-href': route('admin.admin-access-lists.contracts.create', { admin_access_list: user_id })
                }
              }
            ],
            initComplete: function() {
              // Move the buttons container near the search bar
              // $('.dt-buttons', this.api().table().container()).appendTo($('.dataTables_filter', this.api().table().container()));
            },
            drawCallback: function(settings) {
              $('[data-bs-toggle="tooltip"]').tooltip('dispose'); // Dispose of any existing tooltips to prevent potential issues
              $('[data-bs-toggle="tooltip"]').tooltip(); // Re-initialize tooltips
            }
          });
        }
      });
    </script>
@endpush
