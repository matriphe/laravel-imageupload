<!DOCTYPE html>
<html>
    <head>
        <title>Imageupload</title>
    </head>
    <body>
        <form action="{{ URL::current() }}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ Session::token() }}">
            <pre>{{ (!empty($result) ? print_r($result, 1) : '') }}</pre>
            <div>
                <input type="file" name="file">
            </div>
            <div>
                <button type="submit">Upload!</button>
            </div>
        </form>
    </body>
</html>