<img src="@if(!$userdata->photo) {{ asset('images/user.png') }} @else data:image.jpg;base64,{{$userdata->photo}} @endif" class="@if(isset($fluid)) img-fluid @endif rounded-circle @if(isset($border)) {{$border}} @endif" alt="" width="auto" height="100%">