<?php 

class User extends \Helper\Controller {

	function afterroute($f3) {
		header('Content-Type: application/json');
		echo View::instance()->render('layouts/default.json');
	}
	/** 
	* login
	**/
	function auth($f3) {
		$request = json_decode($f3->get('BODY'),true);
		$rules = [];
        $rules['password'] = 'required';
        if (!array_key_exists('email',$request) && !array_key_exists('username',$request)) {
            $rules['email'] = 'required';
        }
        $valid = \Helper\Validate::is_valid($request, $rules);
		if (!array_key_exists('email',$request)) {
            $request['email'] = '';
        }
		if (!array_key_exists('username',$request)) {
            $request['username'] = '';
        }
        if ($valid === true) {
            $user = new \Model\User();
            $user->load(['email=? OR username=?', $request['email'], $request['username']]);
			if ($user->valid() && password_verify($request['password'], $user->password)) {
                $data = $user->cast(); unset($data['password']);
				$f3->set('data',[
                    'status'  => 200,
                    'success' => true,
                    'message' => $f3->get('LANG.V1.response.signin.success'),
                    'token'   => \Helper\Token::encode([
                        'uid'    => $user->uid,
                        'rights' => $user->rights,
                    ], $f3->APPKEY),
                    'content' => $data
                ]);
            } else {
				$f3->set('data',[
                    'status'  => 401,
                    'message' => 'Authentication failed'
                ]);
            }
        } else {
            $f3->set('data',['errors' => $valid]);
        }
	}

	/** 
	* Terminate session
	**/
	function logout($f3) {
		$f3->clear('SESSION');
	}

	/**
	 * Create user
	 */
	function create() {
		$request = json_decode($f3->get('BODY'),true);
		$rules = [
            'email'    => 'required|valid_email|dbunique,\V1\Models\Users',
            'username' => 'required|min_len,3|max_len,25|alpha_dash|dbunique,\V1\Models\Users',
            'password' => 'required|min_len,6|max_len,25',
            'confirm'  => 'required|equalsfield,password',
        ];
        $valid = \Validate::is_valid($request, $rules);
        if ($valid === true) {
            $data = $request;
            $data['uid'] = true;
            $data['rights'] = ['member'];
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $user = new \Model\Users();
            $user->copyfrom($user->clean($data));
            $user->touch('created');
            if ($user->save()) {
                $data = $user->cast(); unset($data['password']);
                $this->response->set([
                    'status'  => 201,
                    'success' => true,
                    'message' => $app->get('LANG.V1.response.signup.success'),
                    'token'   => \Token::encode([
                        'uid'    => $data['uid'],
                        'rights' => $data['rights'],
                    ], $app->APPKEY),
                    'content' => $data
                ]);
            } else {
                $this->response->set([
                    'status'  => 400,
                    'message' => $app->get('LANG.V1.response.signup.failure')
                ]);
            }
        } else {
            $this->response->set('errors', $valid);
        }
	}
}