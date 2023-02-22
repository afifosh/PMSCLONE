<div class="sidebar-file-manager">
  <div class="sidebar-inner">
    <!-- sidebar menu links starts -->
    <!-- add file button -->
    <div class="dropdown dropdown-actions">
      <button
        class="btn btn-primary add-file-btn text-center w-100"
        type="button"
        id="addNewFile"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="true"
        disabled
      >
        <span class="align-middle">Add New</span>
      </button>
      <div class="dropdown-menu" aria-labelledby="addNewFile">
        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#new-folder-modal">
          <div class="mb-0">
            <i data-feather="folder" class="me-25"></i>
            <span class="align-middle">Folder</span>
          </div>
        </button>
        <button class="dropdown-item upload-file-modal">
          <div class="mb-0" for="file-upload">
            <i data-feather="upload-cloud" class="me-25"></i>
            <span class="align-middle">File Upload</span>
            {{-- <input type="file" id="file-upload" hidden /> --}}
          </div>
        </button>
        <button class="dropdown-item">
          <div for="folder-upload" class="mb-0">
            <i data-feather="upload-cloud" class="me-25"></i>
            <span class="align-middle">Folder Upload</span>
            {{-- <input type="file" id="folder-upload" webkitdirectory mozdirectory hidden /> --}}
          </div>
        </button>
      </div>
    </div>
    <!-- add file button ends -->

    <!-- sidebar list items starts  -->
    <div class="sidebar-list">
      <!-- links for file manager sidebar -->
      <div class="list-group">
        <div class="my-drive"></div>
        <a href="{{route('admin.shared-files.index')}}" class="list-group-item list-group-item-action {{request()->route()->getName() == 'admin.shared-files.index' && !request('filter') ? 'active' : ''}}">
          <i data-feather="clock" class="me-50 font-medium-3"></i>
          <span class="align-middle">Recents</span>
        </a>
        <a href="{{route('admin.shared-files.index', ['filter' => 'important'])}}" class="list-group-item list-group-item-action {{request('filter') == 'important' ? 'active' : ''}}">
          <i data-feather="star" class="me-50 font-medium-3"></i>
          <span class="align-middle">Important</span>
        </a>
        <a href="{{route('admin.shared-files.index', ['filter' => 'trash'])}}" class="list-group-item list-group-item-action {{request('filter') == 'trash' ? 'active' : ''}}">
          <i data-feather="trash" class="me-50 font-medium-3"></i>
          <span class="align-middle">Trash</span>
        </a>
      </div>
      <div class="list-group list-group-labels">
        <h6 class="section-label px-4 mb-1">Labels</h6>
        <a href="{{route('admin.shared-files.index', ['filter' => 'documents'])}}" class="list-group-item list-group-item-action {{request('filter') == 'documents' ? 'active' : ''}}">
          <i data-feather="file-text" class="me-50 font-medium-3"></i>
          <span class="align-middle">Documents</span>
        </a>
        <a href="{{route('admin.shared-files.index', ['filter' => 'images'])}}" class="list-group-item list-group-item-action {{request('filter') == 'images' ? 'active' : ''}}">
          <i data-feather="image" class="me-50 font-medium-3"></i>
          <span class="align-middle">Images</span>
        </a>
        <a href="{{route('admin.shared-files.index', ['filter' => 'videos'])}}" class="list-group-item list-group-item-action {{request('filter') == 'videos' ? 'active' : ''}}">
          <i data-feather="video" class="me-50 font-medium-3"></i>
          <span class="align-middle">Videos</span>
        </a>
        <a href="{{route('admin.shared-files.index', ['filter' => 'audios'])}}" class="list-group-item list-group-item-action {{request('filter') == 'audios' ? 'active' : ''}}">
          <i data-feather="music" class="me-50 font-medium-3"></i>
          <span class="align-middle">Audio</span>
        </a>
        <a href="{{route('admin.shared-files.index', ['filter' => 'archives'])}}" class="list-group-item list-group-item-action {{request('filter') == 'archives' ? 'active' : ''}}">
          <i data-feather="layers" class="me-50 font-medium-3"></i>
          <span class="align-middle">Archives</span>
        </a>
      </div>
      <!-- links for file manager sidebar ends -->

      <!-- storage status of file manager starts-->
      <div class="storage-status mb-5 px-4">
        <h6 class="section-label mb-1">Storage Status</h6>
        <div class="d-flex align-items-center cursor-pointer">
          <i data-feather="server" class="font-large-1"></i>
          <div class="file-manager-progress ms-1">
            <span>68GB used of 100GB</span>
            <div class="progress progress-bar-primary my-50" style="height: 6px">
              <div
                class="progress-bar"
                role="progressbar"
                aria-valuenow="80"
                aria-valuemin="80"
                aria-valuemax="100"
                style="width: 80%"
              ></div>
            </div>
          </div>
        </div>
      </div>
      <!-- storage status of file manager ends-->
    </div>
    <!-- side bar list items ends  -->
    <!-- sidebar menu links ends -->
  </div>
</div>
