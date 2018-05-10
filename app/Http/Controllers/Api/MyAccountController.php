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

    public function user() {
        return Auth::user();
    }

   /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show()
    {
        return new UserResource($this->user());
    }

    /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->validate(
            $this->request,
            $this->model->getValidationRules($this->user()->id)
        );

        $model = $this->user();
        $model->fill($this->sanatizeRequestData());
        $this->updateAdminAttrs($model);

        $model->save();
        return new UserResource($model);

    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $model = $this->user();
        $this->authorize($model);
        $model->delete();
        return $this->response->array([
            'message' => 'deleted',
        ]);
    }


    /**
     * Update the admin attributes on a model
     * @param  User   &$model User getting updated
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

    /**
     * Update the request data with an
     * encrypted version of the password
     */
    private function sanatizeRequestData()
    {
        $keys = [
            'username',
            'email',
            'first_name',
            'last_name'
        ];

        $data = [];
        foreach ($keys as $key) {
            if ($value = $this->request->get($key)) {
                $data[$key] = $value;
            }
        }

        if ($password = $this->request->get('password')) {
            $data['password'] = bcrypt($password);
        }

        return $data;
    }
}
