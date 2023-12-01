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
      openLevel: 150
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

          var childTableId = 'programs-child-table-' + user_id;
          var childTable = row.child('<table id="' + childTableId + '" class="display table dataTable" style="" width="100%"></table>').show();
          var content = `
          <tr class="child-row-added p-0">
              <td class="p-0" colspan="100%">
                      <div class="" id="child-phases-${user_id}">
                          <!-- Phases content here -->
                          <!-- This is where your child table (Datatable) will go -->
                          <table class="table" id="programs-child-table-${user_id}"></table>
                      </div>
              </td>
          </tr>
          `;
          var contentRow = $(content);
          contentRow.addClass("custom-content-row");
          contentRow.css("background-color", "#f9f9f9");
          $(row.node()).next().after(contentRow);
          var childTableId = "programs-child-table-" + user_id;
          $('#' + childTableId).DataTable({
            processing: true,
          serverSide: true,
          ajax: route('admin.admin-access-lists.programs.index', { admin_access_list: user_id }),
          columns: [
            { data: "id", "name": "programs.id", title: 'ID' },
            { data: "name", "name": "programs.name", title: 'Program Name' },
            { data: "granted_till", title: 'Granted Till' },
            { data: "actions", title: 'Actions', orderable: false, searchable: false }
          ],
          buttons: [],
          destroy: true,
          dom: 'Blfrtip',
          });
        }
      });
    </script>
@endpush
