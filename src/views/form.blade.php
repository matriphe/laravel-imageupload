<!DOCTYPE html>
<html>
  <head>
    <title>Imageupload</title>
  </head>
  <body>
    <form action="{{ URL::current() }}" method="post" enctype="multipart/form-data">
      <div>
      <input type="file" name="file" />
      </div>
      <div>
      <button type="submit">Upload!</button>
      </div>
    </form>
  </body>
</html>