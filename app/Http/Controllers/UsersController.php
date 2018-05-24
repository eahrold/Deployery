<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class UsersController extends Controller
{
    public function __construct(BaseRequest $request, User $model)
    {
        parent::__construct($request, $model);
    }

    /**
     * Display a listing of the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = $this->model->all();
        return view('auth.users', compact('models'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('auth.users_form', ['model' => $this->model]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $model = $this->upsert();
        if ($this->request->get('exit')) {
            return redirect()->route('users.index');
        }
        return redirect()->route('users.edit', $model->id);
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = $this->model->findOrFail($id);
        return view('auth.users_form', compact('model'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        $this->upsert($id);
        if ($this->request->get('exit')) {
            if (Auth::user()->is_admin) {
                return redirect()->route('users.index');
            }
            return redirect()->route('projects.index');
        }
        return redirect()->route('users.edit', $id);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $model = $this->model->findOrFail($id);
        if($model->projects()->count()) {
            return redirect()->back()->withErrors([
                "{$model->email} currently owns projects and cannot be deleted at this time"
            ]);
        }

        $this->authorize($model);

        $model->delete();
        return redirect()->route('users.index');
    }


    /**
     * @param integer $id
     */
    private function upsert($id = null)
    {
        $data = array_filter(request()->validate(
            $this->model->getValidationRules($id)
        ));

        $model = $this->model->findOrNew($id);
        $model->fill($data);

        $this->updateAdminAttrs($model);

        $model->save();
        return $model;
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
        if (Auth::user()->can('modifyAdminAttributes', $model)) {
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
