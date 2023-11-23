{{-- Assuming $status is passed to this partial view --}}
<style>
:root {
  --clr-timeline-line-color:var(--clr-color-neutral-500);
  --clr-timeline-step-header-color:var(--clr-color-neutral-600);
  --clr-timeline-step-title-color:var(--clr-color-neutral-700);
  --clr-timeline-step-description-color:var(--clr-color-neutral-700);
  --clr-timeline-incomplete-step-color:var(--clr-color-neutral-600);
  --clr-timeline-current-step-color:var(--clr-color-action-600);
  --clr-timeline-success-step-color:var(--clr-color-success-400);
  --clr-timeline-error-step-color:var(--clr-color-danger-800);
  --clr-timeline-step-title-font-weight:var(--clr-p2-font-weight)
}
.clr-timeline {
  display:flex;
  padding:.6rem
}
.clr-timeline-step {
  display:flex;
  flex-direction:column;
  width:100%;
  min-width:8.75rem;
  margin-left:.6rem
}
.clr-timeline-step clr-icon {
  height:1.8rem;
  width:1.8rem;
  min-height:1.8rem;
  min-width:1.8rem
}
.clr-timeline-step clr-icon[shape=circle] {
  color:#8c8c8c;
  color:var(--clr-timeline-incomplete-step-color,#8c8c8c)
}
.clr-timeline-step clr-icon[shape=dot-circle] {
  color:#0072a3;
  color:var(--clr-timeline-current-step-color,#0072a3)
}
.clr-timeline-step clr-icon[shape=success-standard] {
  color:#5eb715;
  color:var(--clr-timeline-success-step-color,#5eb715)
}
.clr-timeline-step clr-icon[shape=error-standard] {
  color:#c21d00;
  color:var(--clr-timeline-error-step-color,#c21d00)
}
.clr-timeline-step:not(:last-of-type) .clr-timeline-step-body:before {
  content:"";
  background:#b2b3b3;
  background:var(--clr-timeline-line-color,#b2b3b3);
  height:.1rem;
  width:calc(100% - .9rem - .1rem);
  transform:translate(1.7rem,-.95rem)
}
.clr-timeline-step-header {
  color:#8c8c8c;
  color:var(--clr-timeline-step-header-color,#8c8c8c);
  font-size:.65rem;
  line-height:.9rem;
  white-space:nowrap;
  margin-bottom:.4rem
}
.clr-timeline-step-body {
  display:flex;
  flex-direction:column
}
.clr-timeline-step-title {
  color:#666;
  color:var(--clr-timeline-step-title-color,#666);
  font-size:.65rem;
  font-weight:500;
  font-weight:var(--clr-timeline-step-title-font-weight,500);
  line-height:.9rem;
  margin-top:.4rem;
  margin-bottom:.3rem
}
.clr-timeline-step-description {
  color:#666;
  color:var(--clr-timeline-step-description-color,#666);
  font-size:.55rem;
  line-height:.8rem
}
.clr-timeline-step-description button {
  display:block;
  margin-top:.4rem
}
.clr-timeline-step-description img {
  width:100%;
  margin-top:.4rem
}
.clr-timeline.clr-timeline-vertical {
  flex-direction:column;
  min-width:16rem
}
.clr-timeline.clr-timeline-vertical .clr-timeline-step {
  flex-direction:row;
  margin-left:0;
  position:relative
}
.clr-timeline.clr-timeline-vertical .clr-timeline-step:not(:last-of-type) {
  margin-bottom:1.8rem
}
.clr-timeline.clr-timeline-vertical .clr-timeline-step:not(:last-of-type) .clr-timeline-step-body:before {
  position:absolute;
  width:.1rem;
  height:calc(100% + .2rem);
  transform:translate(-1.55rem,1.4rem)
}
.clr-timeline.clr-timeline-vertical .clr-timeline-step-header {
  text-align:right;
  white-space:normal;
  word-break:break-word;
  width:3rem;
  min-width:3rem;
  margin-right:.6rem;
  margin-top:.3rem;
  margin-bottom:0
}
.clr-timeline.clr-timeline-vertical .clr-timeline-step-title {
  margin-top:0
}
.clr-timeline.clr-timeline-vertical .clr-timeline-step-body {
  display:flex;
  flex-direction:column;
  min-width:8.9rem;
  margin-left:.6rem;
  margin-top:.3rem
}
@keyframes spin {
  0% {
    transform:rotate(0deg)
  }
  to {
    transform:rotate(1turn)
  }
}
clr-icon {
  display:inline-block;
  margin:0;
  height:16px;
  width:16px;
  vertical-align:middle;
  fill:currentColor
}
clr-icon .transparent-fill-stroke {
  stroke:currentColor
}
clr-icon.is-green,
clr-icon.is-success {
  fill:#2e8500
}
clr-icon.is-green .transparent-fill-stroke,
clr-icon.is-success .transparent-fill-stroke {
  stroke:#2e8500
}
clr-icon.is-danger,
clr-icon.is-error,
clr-icon.is-red {
  fill:#e02200
}
clr-icon.is-danger .transparent-fill-stroke,
clr-icon.is-error .transparent-fill-stroke,
clr-icon.is-red .transparent-fill-stroke {
  stroke:#e02200
}
clr-icon.is-warning {
  fill:#c27b00
}
clr-icon.is-warning .transparent-fill-stroke {
  stroke:#c27b00
}
clr-icon.is-blue,
clr-icon.is-info {
  fill:#0077b8
}
clr-icon.is-blue .transparent-fill-stroke,
clr-icon.is-info .transparent-fill-stroke {
  stroke:#0077b8
}
clr-icon.is-inverse,
clr-icon.is-white {
  fill:#fff
}
clr-icon.is-inverse .transparent-fill-stroke,
clr-icon.is-white .transparent-fill-stroke {
  stroke:#fff
}
clr-icon.is-highlight {
  fill:#0077b8
}
clr-icon.is-highlight .transparent-fill-stroke {
  stroke:#0077b8
}
clr-icon[dir=up] svg,
clr-icon[shape$=" up"] svg {
  transform:rotate(0deg)
}
clr-icon[dir=down] svg,
clr-icon[shape$=" down"] svg {
  transform:rotate(180deg)
}
clr-icon[dir=right] svg,
clr-icon[shape$=" right"] svg {
  transform:rotate(90deg)
}
clr-icon[dir=left] svg,
clr-icon[shape$=" left"] svg {
  transform:rotate(270deg)
}
clr-icon[flip=horizontal] svg {
  transform:scale(-1) rotateX(180deg)
}
clr-icon[flip=vertical] svg {
  transform:scale(-1) rotateY(180deg)
}
clr-icon .clr-i-badge {
  fill:#e02200
}
clr-icon .clr-i-badge .transparent-fill-stroke {
  stroke:#e02200
}
clr-icon>* {
  height:100%;
  width:100%;
  display:block;
  pointer-events:none
}
clr-icon>svg {
  transition:inherit
}
clr-icon .clr-i-outline--alerted:not(.clr-i-outline),
clr-icon .clr-i-outline--badged:not(.clr-i-outline),
clr-icon .clr-i-solid,
clr-icon .clr-i-solid--alerted,
clr-icon .clr-i-solid--badged,
clr-icon>svg title {
  display:none
}
clr-icon[class*=has-alert] .can-alert .clr-i-outline--alerted {
  display:block
}
clr-icon[class*=has-alert] .can-alert .clr-i-outline:not(.clr-i-outline--alerted) {
  display:none
}
clr-icon[class*=has-badge] .can-badge .clr-i-outline--badged {
  display:block
}
clr-icon[class*=has-badge] .can-badge .clr-i-outline:not(.clr-i-outline--badged) {
  display:none
}
clr-icon.is-solid .has-solid .clr-i-solid {
  display:block
}
clr-icon.is-solid .has-solid .clr-i-outline,
clr-icon.is-solid .has-solid .clr-i-outline--badged,
clr-icon.is-solid .has-solid .clr-i-solid--alerted:not(.clr-i-solid),
clr-icon.is-solid .has-solid .clr-i-solid--badged:not(.clr-i-solid) {
  display:none
}
clr-icon.is-solid[class*=has-badge] .can-badge.has-solid .clr-i-solid--badged {
  display:block
}
clr-icon.is-solid[class*=has-badge] .can-badge.has-solid .clr-i-outline,
clr-icon.is-solid[class*=has-badge] .can-badge.has-solid .clr-i-outline--badged,
clr-icon.is-solid[class*=has-badge] .can-badge.has-solid .clr-i-solid:not(.clr-i-solid--badged) {
  display:none
}
clr-icon.is-solid[class*=has-alert] .can-alert.has-solid .clr-i-solid--alerted {
  display:block
}
clr-icon.is-solid[class*=has-alert] .can-alert.has-solid .clr-i-outline,
clr-icon.is-solid[class*=has-alert] .can-alert.has-solid .clr-i-outline--alerted,
clr-icon.is-solid[class*=has-alert] .can-alert.has-solid .clr-i-solid:not(.clr-i-solid--alerted) {
  display:none
}
clr-icon.has-badge--success .clr-i-badge {
  fill:#2e8500
}
clr-icon.has-badge--success .clr-i-badge .transparent-fill-stroke {
  stroke:#2e8500
}
clr-icon.has-badge--error .clr-i-badge {
  fill:#e02200
}
clr-icon.has-badge--error .clr-i-badge .transparent-fill-stroke {
  stroke:#e02200
}
clr-icon.has-badge--info .clr-i-badge {
  fill:#0077b8
}
clr-icon.has-badge--info .clr-i-badge .transparent-fill-stroke {
  stroke:#0077b8
}
clr-icon.has-alert .clr-i-alert {
  fill:#c27b00
}
clr-icon.has-alert .clr-i-alert .transparent-fill-stroke {
  stroke:#c27b00
}
clr-icon .is-off-screen {
  position:fixed!important;
  border:none!important;
  height:1px!important;
  width:1px!important;
  left:0!important;
  top:-1px!important;
  overflow:hidden!important;
  padding:0!important;
  margin:0 0 -1px!important
}
:root .progress-block>label,
_:-ms-input-placeholder .progress-block>label {
  display:inline-block
}
.spinner {
  position:relative;
  display:inline-block;
  height:3.6rem;
  width:3.6rem;
  min-height:3.6rem;
  min-width:3.6rem;
  -webkit-animation:spin 1s linear infinite;
  animation:spin 1s linear infinite;
  margin:0;
  padding:0;
  background:url("data:image/svg+xml;charset=utf8,%3Csvg%20id%3D%22Layer_2%22%20data-name%3D%22Layer%202%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2072%2072%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3Cstyle%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-1%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-2%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20fill%3A%20none%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-miterlimit%3A%2010%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-width%3A%205px%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-1%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke%3A%20%23000000%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-opacity%3A%200.15%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-2%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke%3A%20%230072a3%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%3C%2Fstyle%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Ctitle%3EPreloader_72x2%3C%2Ftitle%3E%0A%20%20%20%20%3Ccircle%20class%3D%22cls-1%22%20cx%3D%2236%22%20cy%3D%2236%22%20r%3D%2233%22%2F%3E%0A%20%20%20%20%3Cpath%20class%3D%22cls-2%22%20d%3D%22M14.3%2C60.9A33%2C33%2C0%2C0%2C1%2C36%2C3%22%3E%0A%20%20%20%20%3C%2Fpath%3E%0A%3C%2Fsvg%3E%0A");
  text-indent:100%;
  overflow:hidden;
  white-space:nowrap
}
.spinner.spinner-md {
  height:1.8rem;
  width:1.8rem;
  min-height:1.8rem;
  min-width:1.8rem
}
.spinner.spinner-inline,
.spinner.spinner-sm {
  height:.9rem;
  width:.9rem;
  min-height:.9rem;
  min-width:.9rem
}
.spinner.spinner-inline {
  vertical-align:text-bottom
}
.spinner.spinner-inverse {
  background:url("data:image/svg+xml;charset=utf8,%3Csvg%20id%3D%22Layer_2%22%20data-name%3D%22Layer%202%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2072%2072%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3Cstyle%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-1%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-2%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20fill%3A%20none%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-miterlimit%3A%2010%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-width%3A%205px%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-1%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke%3A%20%23ffffff%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-opacity%3A%200.15%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-2%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke%3A%20%2374c1e2%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%3C%2Fstyle%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Ctitle%3EPreloader_72x2%3C%2Ftitle%3E%0A%20%20%20%20%3Ccircle%20class%3D%22cls-1%22%20cx%3D%2236%22%20cy%3D%2236%22%20r%3D%2233%22%2F%3E%0A%20%20%20%20%3Cpath%20class%3D%22cls-2%22%20d%3D%22M14.3%2C60.9A33%2C33%2C0%2C0%2C1%2C36%2C3%22%3E%0A%20%20%20%20%3C%2Fpath%3E%0A%3C%2Fsvg%3E%0A")
}
.spinner.spinner-neutral-0 {
  background:url("data:image/svg+xml;charset=utf8,%3Csvg%20id%3D%22Layer_2%22%20data-name%3D%22Layer%202%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2072%2072%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3Cstyle%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-1%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-2%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20fill%3A%20none%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-miterlimit%3A%2010%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-width%3A%205px%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-1%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke%3A%20%23transparent%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke-opacity%3A%201%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%20%20%20%20.cls-2%20%7B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20stroke%3A%20%23ffffff%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%7D%0A%20%20%20%20%20%20%20%20%3C%2Fstyle%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Ctitle%3EPreloader_72x2%3C%2Ftitle%3E%0A%20%20%20%20%3Ccircle%20class%3D%22cls-1%22%20cx%3D%2236%22%20cy%3D%2236%22%20r%3D%2233%22%2F%3E%0A%20%20%20%20%3Cpath%20class%3D%22cls-2%22%20d%3D%22M14.3%2C60.9A33%2C33%2C0%2C0%2C1%2C36%2C3%22%3E%0A%20%20%20%20%3C%2Fpath%3E%0A%3C%2Fsvg%3E%0A")
}
.spinner.spinner-check {
  -webkit-animation:none;
  animation:none;
  background:url("data:image/svg+xml;charset=utf8,%3Csvg%20version%3D%221.1%22%20viewBox%3D%220%200%2036%2036%22%20preserveAspectRatio%3D%22xMidYMid%20meet%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%20focusable%3D%22false%22%20aria-hidden%3D%22true%22%20role%3D%22img%22%3E%3Cpath%20fill%3D%22%230072a3%22%20class%3D%22clr-i-outline%20clr-i-outline-path-1%22%20d%3D%22M13.72%2C27.69%2C3.29%2C17.27a1%2C1%2C0%2C0%2C1%2C1.41-1.41l9%2C9L31.29%2C7.29a1%2C1%2C0%2C0%2C1%2C1.41%2C1.41Z%22%3E%3C%2Fpath%3E%3C%2Fsvg%3E")
}
.alert-app-level .alert-item .btn .spinner,
.btn-sm .spinner {
  height:.65rem;
  width:.65rem;
  min-height:.65rem;
  min-width:.65rem
}
@-webkit-keyframes spin {
  0% {
    transform:rotate(0deg)
  }
  to {
    transform:rotate(1turn)
  }
}


</style>    
<span class="review-status-class">
    <ul class="clr-timeline clr-timeline-vertical-disable m-0 snipcss-ltkeg"> 
        @foreach($status as $stage)
            @php
                $statusClass = '';
                $statusContent = '';

                switch ($stage['status']) {
                    case 'Not started':
                        $statusClass = 'clr-timeline-step disabled';
                        $statusContent = '<clr-icon shape="circle" aria-label="Not started" role="none"><svg version="1.1" class="has-solid " viewBox="0 0 36 36" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" focusable="false" role="img">
                <path d="M18,34A16,16,0,1,1,34,18,16,16,0,0,1,18,34ZM18,4A14,14,0,1,0,32,18,14,14,0,0,0,18,4Z" class="clr-i-outline clr-i-outline-path-1"></path>
                <path d="M18,34A16,16,0,1,1,34,18,16,16,0,0,1,18,34Z" class="clr-i-solid clr-i-solid-path-1"></path>
            </svg></clr-icon>';
                        break;

                    case 'In progress':
                        $statusClass = 'clr-timeline-step';
                        $statusContent = '<clr-spinner clrmedium="" aria-label="In progress" aria-busy="true" class="spinner spinner-md">Fetching data</clr-spinner>';
                        break;

                    case 'Completed':
                        $statusClass = 'clr-timeline-step';
                        $statusContent = '<clr-icon shape="success-standard" aria-label="Completed" role="none"><svg version="1.1" class="has-solid " viewBox="0 0 36 36" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" focusable="false" role="img">
                <path class="clr-i-outline clr-i-outline-path-1" d="M18,2A16,16,0,1,0,34,18,16,16,0,0,0,18,2Zm0,30A14,14,0,1,1,32,18,14,14,0,0,1,18,32Z"></path>
                <path class="clr-i-outline clr-i-outline-path-2" d="M28,12.1a1,1,0,0,0-1.41,0L15.49,23.15l-6-6A1,1,0,0,0,8,18.53L15.49,26,28,13.52A1,1,0,0,0,28,12.1Z"></path>
                <path class="clr-i-solid clr-i-solid-path-1" d="M18,2A16,16,0,1,0,34,18,16,16,0,0,0,18,2ZM28.45,12.63,15.31,25.76,7.55,18a1.4,1.4,0,0,1,2-2l5.78,5.78L26.47,10.65a1.4,1.4,0,1,1,2,2Z"></path>
            </svg></clr-icon>';
                        break;
                }
            @endphp

            <li class="{{ $statusClass }}">
                <div class="clr-timeline-step-header">{{$stage['last_review_date'] }}</div>
                {!! $statusContent !!}
                <div class="clr-timeline-step-body">
                    <span class="clr-timeline-step-title">{{ $stage['stage_name'] }}</span>
                </div>
            </li>
        @endforeach
    </ul>
</span>
