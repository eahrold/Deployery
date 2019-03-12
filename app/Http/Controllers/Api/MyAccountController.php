<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseRequest;
use App\Http\Resources\Management\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class MyAccountController extends Controller
{
    protected $request;
    protected $model;

    public function __construct(BaseRequest $request, User $model)
    {
        $this->request = $request;
        $this->model = $model;
    }

    /**
     * Get The Current User
     *
     * @return Auth The currrent authenticated user
     */
    public function user() {
        return Auth::guard()->user();
    }

   /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     *
     * @return UserResource
     */
    public function show()
    {
        return new UserResource($this->user());
    }

    /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     *
     * @return UserResource
     */
    public function update()
    {
        $model = $this->user();

        $data = array_filter(request()->validate(
            $this->model->getValidationRules($model->getAuthIdentifier())
        ));

        $model->fill($data);
        $this->updateAdminAttrs($model);

        $model->save();
        return new UserResource($model);

    }

    /**
     * Remove the specified user from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $model = $this->user();
        $this->authorize($model);
        $model->delete();

        return response()->json([
            'message' => 'deleted',
        ]);
    }


    /**
     * Update the admin attributes on a model
     *
     * @param  User   &$model User getting updated
     *
     * @return void
     */
    private function updateAdminAttrs(User $model)
    {
        // Only admins can change an admin status,
        // But they can't remove their own admin status...
        if ($this->user()->can('modifyAdminAttributes', $model)) {
            $admin_attributes = [
                'is_admin',
                'can_manage_teams',
                'can_join_teams',
            ];

            foreach ($admin_attributes as $attr) {
                $model->{$attr} = $this->request->get($attr) ? true : false;
            }
        }
    }
}
