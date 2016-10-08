<?php

namespace App\Http\Controllers\Teamwork;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Mpociot\Teamwork\Facades\Teamwork;
use Mpociot\Teamwork\TeamInvite;

class TeamMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the members of the given team.
     *
     * @param  int $team_id
     * @return \Illuminate\Http\Response
     */
    public function show($team_id)
    {
        $team = $this->getTeam($team_id);
        return view('teamwork.members.list')->withTeam($team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $team_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($team_id, $user_id)
    {
        $team = $this->getTeam($team_id);
        $user = $this->getUser($user_id);

        $this->authorize($team);

        $user->detachTeam($team);

        return redirect(route('teams.index'));
    }


    /**
     * Join the team
     *
     * @param int $team_id
     * @return \Illuminate\Http\Response
     */
    public function join($id)
    {
        $user = $this->getUser(auth()->user()->id);
        if($user->can('joinTeams', Team::class)){
            $user->attachTeam($id);
        }
        return redirect(route('teams.index'));
    }

    /**
     * Leave the specified resource from storage.
     *
     * @param int $team_id
     *
     * @return \Illuminate\Http\Response
     */
    public function leave($team_id)
    {
        $team = $this->getTeam($team_id);
        $user = $this->getUser(auth()->user()->id);

        if ($user->isOwnerOfTeam($team)) {
            abort(403, 'The Owner cannot leave the team.');
        }

        $user->detachTeam($team);

        if($user->teams->count()){
            return redirect()->back();
        }
        return redirect('/');
    }

    /**
     * @param Request $request
     * @param int $team_id
     * @return $this
     */
    public function invite(Request $request, $team_id)
    {
        $team = $this->getTeam($team_id);
        $user = $this->getUser(auth()->user()->id);
        $this->authorize($team);

        if( !Teamwork::hasPendingInvite( $request->email, $team) )
        {
            Teamwork::inviteToTeam( $request->email, $team, function( $invite )
            {
                $this->mailInvite($invite);
            });
        } else {
            return redirect()->back()->withErrors([
                'email' => 'The email address is already invited to the team.'
            ]);
        }

        return redirect(route('teams.members.show', $team->id));
    }

    /**
     * Resend an invitation mail.
     *
     * @param $invite_id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function resendInvite($invite_id)
    {
        $invite = TeamInvite::findOrFail($invite_id);
        $this->mailInvite($invite);
        return redirect(route('teams.members.show', $invite->team));
    }

    private function getUser($user_id)
    {
        $userModel = config('teamwork.user_model');
        return $userModel::findOrFail($user_id);
    }

    private function getTeam($team_id)
    {
        $teamModel = config('teamwork.team_model');
        return $teamModel::findOrFail($team_id);
    }
    /**
     * Send Email to the user
     *
     * @param  \Mpociot\Teamwork\TeamInvite $invite
     * @return void
     */
    private function mailInvite($invite){
        Mail::send('teamwork.emails.invite', ['team' => $invite->team, 'invite' => $invite], function ($mail) use ($invite) {
            $mail->to($invite->email)
                 ->from($invite->inviter->email)
                 ->subject('Invitation to join team '.$invite->team->name);
        });
    }
}
