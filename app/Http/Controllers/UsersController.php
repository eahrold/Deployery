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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $model = $this->upsert($id);

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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = $this->model->findOrFail($id);
        $this->authorize($model);

        $model->delete();
        return redirect()->route('users.index');
    }


    private function upsert($id = null)
    {
        $this->validate(
            $this->request,
            $this->model->getValidationRules($id)
        );

        if ($id) {
            $model = $this->model->findOrFail($id);
        } else {
            $model = $this->model->newInstance();
        }

        $model->fill($this->sanatizeRequestData());
        $isAdmin = Auth::user()->is_admin;

        // Only admins can change an admin status,
        // But they can't remove their own admin status...
        if ($isAdmin && ($model->id != Auth::user()->id)) {
            $model->is_admin = $this->request
                                    ->get('is_admin') ? true : false;
        }

        $model->save();
        return $model;
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
