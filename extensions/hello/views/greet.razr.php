@style('hello-site', 'hello/css/hello.css')

<p>@transchoice("{0}: No names|one: One name|more: %names% names", names|length, ["%names%" => names|length])<p>

@foreach(names as name)
    <p>
        @trans("Hello %name%!", ["%name%" => name])
    </p>
@endforeach
