<?php

namespace Src\BlendedConcept\Security\Presentation\HTTP;
use Src\BlendedConcept\Security\Application\UseCases\Commands\Role\StoreRoleCommand;
use Src\BlendedConcept\Security\Application\UseCases\Queries\Permissions\GetPermissionwithPagination;
use Src\BlendedConcept\Security\Application\UseCases\Queries\Roles\GetRolewithPagniation;
use Src\Common\Infrastructure\Laravel\Controller;
use Src\BlendedConcept\Security\Application\Requests\StoreRoleRequest;
use Src\BlendedConcept\Security\Application\Requests\UpdateRoleRequest;
use Src\BlendedConcept\Security\Application\DTO\RoleData;
use Src\BlendedConcept\Security\Application\Mappers\RoleMapper;
use Src\BlendedConcept\Security\Application\UseCases\Commands\Role\UpdateRoleCommand;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\BlendedConcept\Security\Application\UseCases\Queries\Roles\getRoleName;
use Inertia\Inertia;
use Src\BlendedConcept\Security\Application\Policies\RolePolicy;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{




    public function index()
    {
        // $this->authorize('view', RoleEloquentModel::class);

       abort_if(authorize('view', RolePolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');


        try {
            // Check if the user is authorized to view roles

            // Get the filters from the request
            $filters = request()->only(['name', 'search', 'perPage']);


            // Retrieve roles with pagination using the provided filters
            $roles = (new GetRolewithPagniation($filters))->handle();

            // Retrieve role names
            $roles_name = (new GetRoleName())->handle();

            // Retrieve permissions with pagination
            $permissions = (new GetPermissionwithPagination([]))->handle();


            // Render the Inertia view with the obtained data
            return Inertia::render('BlendedConcept/Security/Presentation/Resources/Roles/Index', [
                "roles" => $roles['paginate_roles'],
                "roles_name" => $roles_name,
                "permissions" => $permissions["default_permissions"]
            ]);
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('sytemErrorMessage', $e->getMessage());
        }
    }


    /**
     * @param  StoreRoleRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function store(StoreRoleRequest $request)
    {
        // Check if the user is authorized to create roles

       abort_if(authorize('create', RolePolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');


        try {

            // Validate the incoming request data based on the specified rules in StoreRoleRequest
            $request->validated();

            // Create a new role using the
            $newRole = RoleMapper::fromRequest($request);
            $createNewRole = (new  StoreRoleCommand($newRole));
            $createNewRole->execute();
            // Redirect the user to the index page for roles with a success message
            return redirect()->route('roles.index')->with("successMessage", "Roles created Successfully!");
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with("sytemErrorMessage", $e->getMessage());
        }
    }

    /***
     * @param UpdateRoleRequest $request ,RoleEloquentModel $role
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    public function update(UpdateRoleRequest $request, RoleEloquentModel $role)
    {
        // Check if the user is authorized to edit roles
       abort_if(authorize('edit', RolePolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {

            // Create a RoleData object from the request data and the ID of the role to be updated
            $roleData = RoleData::fromRequest($request, $role->id);

            // Create an instance of the UpdateRoleCommand with the role data
            $updateRole = (new UpdateRoleCommand($roleData));

            // Execute the update role command
            $updateRole->execute();

            // Redirect the user to the index page for roles with a success message
            return redirect()->route('roles.index')->with("successMessage", "Role updated Successfully!");
        } catch (\Exception $e) {

            return redirect()->route('roles.index')->with("sytemErrorMessage", $e->getMessage());
        }
    }


    //destroy role
    public function destroy(RoleEloquentModel $role)
    {
        abort_if(authorize('destroy', RolePolicy::class), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $role->delete();
        return redirect()->route('roles.index')->with("successMessage", "Role deleted Successfully!");
    }
}
