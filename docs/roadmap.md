# Deployery Roadmap

## v0.2

### Backend
- [x] User based SSH Key generation used in connecting to Git hosts
- [ ] Slack / HipChat / Email notifications
- [ ] Configure site settings in backend
- [x] Subclass \Remote class to support SFTP zero byte issue
- [ ] Validate the source of the webhook request
- [x] Migrate API Auth to JWT
- [ ] Add unit tests, setup [TravisCI](https://travis-ci.org)
- [x] Setup [Scrutinizer CI](https://scrutinizer-ci.com/pricing)

### Frontend
- [ ] Add Settings page
- [ ] Add confirmation handling to related model delete requests (trash can)
- [ ] Fix secondary nav bar hi-light height
- [ ] Add global deploy button somewhere
- [ ] Add more info to the dashborad
- [ ] Change deployment panel, Make `to` and `from` disabled by default
- [x] Update deployment panel to be filterable.
- [ ] Change local of deploying message on main project page.
- [ ] Display Server IP address and message about whitelisting it on deployment targets
- [ ] Add display of SSH pubkey in server tab.


## v0.3

### Backend
- [ ] Migrate to Users -> Accounts
- [ ] Cancel deployment operation
- [ ] Auto spawn project specific queues
- [ ] Support cloud file storage
- [ ] Do a pre-run test on server to check writability of every directory
- [ ] Improve error handling
- [ ] Explore alternative handling of run scripts, currently they don't support shebang.
- [ ] Support HTTP login for remote git repositories