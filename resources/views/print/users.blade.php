<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <style>
     @page { size: 500pt 500pt; }
     </style> -->
    <!-- <meta charset="utf-8"> -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Report</title>
    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href='https://fonts.googleapis.com/css2?family=Cairo' rel='stylesheet'>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover table-full-width"
                   id="PrintdailyTable">
                <thead>
                <tr>
                    <th class="text-center">{{trans('lang.name')}}</th>
                    <th class="text-center">{{trans('lang.email')}}</th>
                    <th class="text-center">{{trans('lang.phone')}}</th>
                    <th class="text-center">{{trans('lang.created_at')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as  $row)
                    <tr>
                        <td class="text-center">{{$row->name}}</td>
                        <td class="text-center">{{$row->email}}</td>
                        <td class="text-center">{{$row->phone}}</td>
                        <td class="text-center">{{$row->created_at->format('Y-m-d')}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{--<script src="js/bootstrap.min.js"></script>--}}
</body>
</html>
