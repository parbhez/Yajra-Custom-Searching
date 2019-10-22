
<!-- Yajra Custom Searching. Give Input Text and Get All match data in Database -->
<!-- Html Code -->
<section class="content">
   <!-- Custom Tabs -->
   <div class="nav-tabs-custom">
      <form name="searchForm" class="searchForm form-horizontal" method="post" action="{{route('hr.getEmployeeReportsData.post')}}">
         <div class="row">
            <div class="form-group" style="margin-top: 10px; margin-bottom: 5px;">
               <div class="col-md-9 col-md-offset-3">
                  <label class="col-md-1"> 
                     Name: 
                  </label>
                  <div class="col-md-3">
                     {{csrf_field()}}
                     <input class="form-control" type="text" name="name" id="name" value="{{$name}}">
                     
                  </div>
                  <label class="col-md-1"> 
                     Email: 
                  </label>
                  <div class="col-md-3">
                      <input class="form-control" type="text" name="email" id="email" value="{{$email}}">
                  </div>
                  <div class="col-md-2">
                     <button class="btn btn-sm btn-primary" type="submit"> Submit </button>
                  </div>
                </div>
            </div>
          </div>
      </form>
       
       <div class="tab-content">
           <div class="tab-pane active">
            <div class="box box-default">
               <div class="box-body">
                     <div class="row">
                         <div class="col-md-12">
                           <table id="employeeReportDatatable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            
                            <tfoot>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                         </div>
                     </div>
                 </div>
                 
            </div>
           </div>
           <!-- /.tab-pane -->
       </div>

  <!-- Yajra DataTable Ajax code-->
  
  <script type="text/javascript">
   $(document).ready(function(){
      var name = $("#name").val();
      var email = $("#email").val();
      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
      $("#employeeReportDatatable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        'order': [[0,"asc"]],

        ajax: {
           url : "{{route('hr.get-test-employee-reports')}}",
           type: "POST",
           dataType: 'json',
           data: {
              'name' : name,
              'email' : email
           }
        },
        columns: [
            { data: 'id', name: 'id', orderable:true, searchable: true },
            { data: 'name', name: 'name', orderable: true, searchable:true },
            { data: 'email', name: 'email', orderable: true, searchable: true },
            { data: 'status', name: 'status', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: true, searchable: false },
        ]
    });
   });
</script> 

<!--Route -->
//Yajara Search
    Route::get('/employeeReports', [
    	'as' => 'hr.employeeReports',
    	'uses' => 'HRController@employeeReports'
    ]);

    Route::any('/getEmployeeReportsData', [
    	'as' => 'hr.getEmployeeReportsData.post',
    	'uses' => 'HRController@employeeReports'
    ]);

    Route::post('get-test-employee-reports', [
    	'as' => 'hr.get-test-employee-reports',
    	'uses' => 'HRController@getTestEmployeeReports'
    ]);

    <!-- Controller Code -->

    public function employeeReports(Request $request)
    {
        //return $request->all();
        $name = $request->name;
        $email = $request->email;
        if($request->name && $request->email){
            $name = $request->name;
            $email = $request->email;
        }
        return view('hr.employee.employeeReports',compact('name','email'));
    }

    public function getTestEmployeeReports(Request $request)
    {
        // return $request->all();
        if(isset($request->name) && isset($request->email)){
            $model = User::orderBy('id','ASC')
                ->where('name','like','%'.$request->name.'%')       
                ->where('email','like','%'.$request->email.'%')       
                ->get(); 
        }else if(isset($request->name)){
            $model = User::orderBy('id','ASC')
                ->where('name','like','%'.$request->name.'%')       
                ->get(); 
        }else if(isset($request->email)){
            $model = User::orderBy('id','ASC')
                ->where('email','like','%'.$request->email.'%')       
                ->get(); 
        }else{
            $model = [];
        }
        // dd($model);
         return Datatables::of($model)
            ->editColumn('status', function ($user) {
                $html = '';
                $html .= "<label class='label label-warning'";
                if ($user->status == 1) {
                    $html .= "style='display:none'";
                }
                $html .= ">Inactive</label>";

                $html .= "<label class='label label-success'";
                if ($user->status == 0) {
                    $html .= "style='display:none'";
                }
                $html .= ">Completed</label>";
                return $html;
            })
            ->addColumn('action', function ($user) {
                $html = '<a href="#" class="btn btn-xs btn-primary"> Edit </a>';
                return $html;
            })
            ->rawColumns(['id','name','email','status', 'action'])
            ->make();
        return view('hr.employee.employeeReports');    
    }
