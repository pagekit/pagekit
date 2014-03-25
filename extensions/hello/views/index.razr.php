@style('hello-site', 'hello/css/hello.css')

@foreach(names as name)
<p>
    Hello @name!
</p>
@endforeach