<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Models\RoleModuleAssign;
use App\Models\RoleModuleName;
use App\Models\RolePermission;
use App\Models\RolePermissionAssign;
use Exception;
use DB;

class RolesAndPermissionController extends Controller
{
    /**
     * Save permission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        /*$services = new AdminService();
        $response = $services->saveRolesAndPermission();
        
        $data = array();
        if ($response != null && $response->status === true) {
            $data = [
                'data' => $response->data
            ];
            return response()->json($data, 200);
        }
        
        return response()->json($data, 422);*/
    }

    /**
     * Get Role Permission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRolePermission(Request $request)
    {
        $request = json_decode($request->getContent() , true);
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try
        {
            $roleIds = $request['role_ids'];
            $roleList = Role::with(['moduleAssign', 'rolePermission'])->select('id', 'name')
                ->whereIn('roles.id', $roleIds)->get()
                ->toArray();

           

            $rolesData = [];
            $i = 0;
            foreach ($roleList as $r_val)
            {
                if (isset($r_val['module_assign']) && $r_val['module_assign'])
                {
                    $rolesData[$i]['id'] = $r_val['id'];
                    $rolesData[$i]['name'] = $r_val['name'];
                    foreach ($r_val['module_assign'] as $m_val)
                    {
                        $moduleList = RoleModuleName::select('id', 'name as module_name')->with(['modulePermission' => function ($query)
                        {
                            $query->select('id as permission_id', 'rl_module_name_id', 'name as permission_name');
                        }
                        ])
                            ->where('id', $m_val['rl_module_name_id'])->get()
                            ->toArray();

                        if (isset($moduleList[0]))
                        {
                            $rolesData[$i]['modules'][] = $moduleList[0];
                        }
                    }
                    if (isset($r_val['role_permission']) && $r_val['role_permission'])
                    {
                        $rIds = array_column($r_val['role_permission'], 'rl_permission_id');
                        $rolesData[$i]['role_permission'] = $rIds;
                    }
                    $i++;
                }
            }

           // print_r($rolesData); exit();

            $users1 = Company::select('id', 'name', 'email')->whereHas('roles', function ($q) use ($roleIds)
            {
                $q->whereIn('id', $roleIds);
            })->get()
                ->toArray();

            foreach ($users1 as $key => $value) {
                $users1[$key]['tabel'] = 'company';
            }

            $users2 = User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS name') , 'email')->whereHas('roles', function ($q) use ($roleIds)
            {
                $q->whereIn('id', $roleIds);
            })->get()
                ->toArray();
                $rolesUsersData = [];
             foreach ($users2 as $key => $value) {
                $users2[$key]['tabel'] = 'users';
            }
             if(isset($request['user_ids'])) 
             {
                
                foreach ($request['user_ids'] as $key => $value) {
                    if($value['tabel'] == 'users') {

                         $usersData = User::select('id', DB::raw('CONCAT(first_name," ", last_name) AS name') , 'email')->with('roles')->where('id',$value['id'])->first();

                         $roleList = Role::with(['moduleAssign'])->select('id', 'name')
                            ->where('roles.id', $usersData->roles[0]->id)->get()
                            ->toArray();

                        $tempData = [];
                        $j = 0;
                        foreach ($roleList as $r_val)
                        {
                            if (isset($r_val['module_assign']) && $r_val['module_assign'])
                            {
                                $tempData[$j]['id'] = $r_val['id'];
                                $tempData[$j]['name'] = $usersData->name;
                                foreach ($r_val['module_assign'] as $m_val)
                                {
                                    $moduleList = RoleModuleName::select('id', 'name as module_name')->with(['modulePermission' => function ($query)
                                    {
                                        $query->select('id as permission_id', 'rl_module_name_id', 'name as permission_name');
                                    }
                                    ])
                                    ->where('id', $m_val['rl_module_name_id'])->get()
                                    ->toArray();

                                    if (isset($moduleList[0]))
                                    {
                                        $tempData[$j]['modules'][] = $moduleList[0];
                                    }
                                }
                    
                                $j++;
                             }

                            $rolesUsersData[] =  $tempData[0];
                        }

                    } else {
                        $usersData = Company::select('id',  'name' , 'email')->with('roles')->where('id',$value['id'])->first();
                         $roleList = Role::with(['moduleAssign'])->select('id', 'name')
                            ->where('roles.id', $usersData->roles[0]->id)->get()
                            ->toArray();

                        $tempData = [];
                        $j = 0;
                        foreach ($roleList as $r_val)
                        {
                            if (isset($r_val['module_assign']) && $r_val['module_assign'])
                            {
                                $tempData[$j]['id'] = $r_val['id'];
                                $tempData[$j]['name'] = $usersData->name;
                                foreach ($r_val['module_assign'] as $m_val)
                                {
                                    $moduleList = RoleModuleName::select('id', 'name as module_name')->with(['modulePermission' => function ($query)
                                    {
                                        $query->select('id as permission_id', 'rl_module_name_id', 'name as permission_name');
                                    }
                                    ])
                                    ->where('id', $m_val['rl_module_name_id'])->get()
                                    ->toArray();

                                    if (isset($moduleList[0]))
                                    {
                                        $tempData[$j]['modules'][] = $moduleList[0];
                                    }
                                }
                    
                                $j++;
                             }

                             $rolesUsersData[] = $tempData[0];
                        }
                    }
                }
             }
            //print_r($rolesData); exit();
            $users = array_merge($users1, $users2);
            //print_r($users); exit();
            $data = ['rolesData' => $rolesData, 'rolesUsersData'=>$rolesUsersData,'users' => $users, ];
            $status = true;
            $message = "get Successfully";
            return $this->generateResponse($status, $message, $data);
        }
        catch(\Exception $e)
        {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }

    }

    /**
     *  Role wise assign Permission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function setRolePermission(Request $request)
    {
        $request = json_decode($request->getContent() , true);
        try
        {
            if (isset($request['rl_module_name_id']))
            {
                $ids = RolePermission::where('rl_module_name_id',$request['rl_module_name_id'])->pluck('id');
                foreach ($ids as $key => $value) {
                    if($request['slug'] == 'create') {
                        $data = RolePermissionAssign::where('role_id',$request['role_id'])->where('rl_permission_id',$value)->first();
                        if($data == '') {
                            $role_permission_assign = new RolePermissionAssign();
                            $role_permission_assign->role_id = $request['role_id'];
                            $role_permission_assign->rl_permission_id = $value;
                            $role_permission_assign->save();
                        }
                    } else {
                        $delete = RolePermissionAssign::where('role_id', $request['role_id'])->where('rl_permission_id', $value)->delete();
                    }
                }

                    $status = true;
                    $message = "save Successfully";
                    return $this->generateResponse($status, $message);      

                
            }
            else
            { 

                if ($request['slug'] == 'create')
                {
                    
                    $role_permission_assign = new RolePermissionAssign();
                    $role_permission_assign->role_id = $request['role_id'];
                    $role_permission_assign->rl_permission_id = $request['permission_id'];
                    $role_permission_assign->save();
                    $status = true;
                    $message = "save Successfully";
                    return $this->generateResponse($status, $message);

                    echo "Hello"; exit();
                }
                else
                {
                    $delete = RolePermissionAssign::where('role_id', $request['role_id'])->where('rl_permission_id', $request['permission_id'])->delete();
                    if ($delete)
                    {
                        $status = true;
                        $message = "remove Successfully";
                        return $this->generateResponse($status, $message);
                    }
                }
            }

        }
        catch(\Exception $e)
        {
            $data = [];
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }
}

