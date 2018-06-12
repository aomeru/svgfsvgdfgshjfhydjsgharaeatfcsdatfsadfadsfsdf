<div>
	@if(Session::has('access_denied'))
	<div class="alert alert-danger bg-danger text-white text-center mb-3" role="alert">
		<i class="fa fa-lock ml5"></i> {!! session('access_denied') !!}
	</div>
	@endif

	@if(Session::has('error'))
    <div class="alert alert-danger bg-danger text-white text-center mb-3" role="alert">
		<i class="fa fa-times ml5"></i> {!! session('error') !!}
	</div>
	@endif

	@if(Session::has('error_array'))
	@foreach(Session::get('error_array') as $ea_item)
	<div class="alert alert-danger bg-danger text-white text-center mb-3" role="alert">
		<i class="fa fa-times ml5"></i> {!! $ea_item !!}
	</div>
	@endforeach
	@endif


	@if(session::has('success'))
    <div class="alert alert-success bg-success text-white text-center mb-3" role="alert">
		<i class="fa fa-check ml5"></i> {!! session('success') !!}
	</div>
	@endif
</div>
