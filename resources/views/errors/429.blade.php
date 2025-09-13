@php
  $code = 429;
  $title = 'Too Many Requests';
  $description = 'Youve hit the rate limit. Please slow down and try again shortly.';
@endphp
@include('errors.page')
