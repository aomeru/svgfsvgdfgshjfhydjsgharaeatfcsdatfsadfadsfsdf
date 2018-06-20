<div>
	@if(Session::has('access_denied'))
	<div class="alert alert-danger bg-danger text-white text-center mb-3 alert-dismissible fade show" role="alert">
		<i class="fas fa-lock ml-2"></i> {!! session('access_denied') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	@endif

	@if(Session::has('error'))
    <div class="alert alert-danger bg-danger text-white text-center mb-3 alert-dismissible fade show" role="alert">
		<i class="fas fa-times ml-2"></i> {!! session('error') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
    @endif

    @if(Session::has('warning'))
    <div class="alert alert-warning bg-warning text-white text-center mb-3 alert-dismissible fade show" role="alert">
		<i class="fas fa-exclamation-triangle ml-2"></i> {!! session('error') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	@endif

	@if(Session::has('error_array'))
	@foreach(Session::get('error_array') as $ea_item)
	<div class="alert alert-danger bg-danger text-white text-center mb-3 alert-dismissible fade show" role="alert">
		<i class="fas fa-times ml-2"></i> {!! $ea_item !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	@endforeach
	@endif

	@if(session::has('success'))
    <div class="alert alert-success bg-success text-white text-center mb-3 alert-dismissible fade show" role="alert">
		<i class="fas fa-check ml-2"></i> {!! session('success') !!}
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	@endif
</div>
