@include('errors.layout', [
  'title'       => $title ?? null,
  'description' => $description ?? null,
  'code'        => $code ?? null,
  'debug'       => $debug ?? null,
])
