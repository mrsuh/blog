# Continuous delivery with Travis CI and Ansible

Setting up [continuous delivery](https://en.wikipedia.org/wiki/Continuous_delivery) for your project with github.com

Requirements:
* a repository on [github.com](https://github.com)
* an Ansible server (example IP: 1.1.1.1)
* a deployment server for your project (example IP: 2.2.2.2)
* a local machine with your project configured
* basic knowledge of command-line tools, [Travis CI](https://travis-ci.org), and [Ansible](https://www.ansible.com)

## Setting Up the Server for Project Deployment

```bash
[ root@2.2.2.2 ] adduser ansible # add a user under which Ansible will connect to the server and in whose directory the project will be deployed.
[ root@2.2.2.2 ] su - ansible
[ ansible@2.2.2.2 ] ssh-keygen -t rsa -b 4096 -C 'github' -f ~/.ssh/github_key # generate a key without a passphrase for deploying the repository.
[ ansible@2.2.2.2 ] eval "$(ssh-agent -s)"
[ ansible@2.2.2.2 ] ssh-add ~/.ssh/github_key
[ ansible@2.2.2.2 ] cat ~/.ssh/github_key.pub # display and copy the public key github_key.pub
```

Add the public key `github_key.pub` to the deploy keys for the repository on github.com
(In the repository settings on github.com, go to the "Deploy keys" tab)

## Settings up Ansible server

```bash
[ root@1.1.1.1 ] yum install ansible
[ root@1.1.1.1 ] adduser ansible # add a user under which Travis will access this server.
[ root@1.1.1.1 ] su - ansible
[ ansible@1.1.1.1 ] ssh-keygen -t rsa -b 4096 -C 'ansible' -f ~/.ssh/ansible_key # generate a key without a passphrase for connecting to the server with the project
[ ansible@1.1.1.1 ] eval "$(ssh-agent -s)"
[ ansible@1.1.1.1 ] ssh-add ~/.ssh/ansible_key
[ ansible@1.1.1.1 ] cat ~/.ssh/ansible_key.pub # display and copy the public key ansible_key.pub
```

Add the public key `ansible_key.pub` to the server of our project.
```bash
[ ansible@2.2.2.2 ] mcedit .ssh/authorized_keys
[ ansible@2.2.2.2 ] chmod 600 .ssh/authorized_keys
```

Add the IP address of our project server to the `hosts.yml` file.
```bash
[ ansible@1.1.1.1 ] mcedit /path/to/ansible/hosts.yml
````

`hosts.yml`
```yaml
[ansible]
2.2.2.2
```

Write a small `playbook` that will deploy the latest version of the `master` branch
```bash
[ ansible@1.1.1.1 ] mcedit /path/to/ansible/playbook.yml
```

`playbook.yml`
```yaml 
- hosts: all
   user: ansible
   tasks:
       - name: Clone git repo
         git:
             repo: ssh://git@github.com/{github_username}/{github_repo}.git
             dest: /home/ansible/var/www/{github_repo}
             version: master
             accept_hostkey: yes
             force: yes
```

## Setting up Travis

Register on the site [travis-ci.org](https://travis-ci.org) using your [github.com](https://github.com) account. Enable integration for the desired repository. 
In the Travis repository settings, enable:
* Build only if .travis.yml is present
* Build branch updates

On your local machine, where your project is deployed, install the `travis` utility and authenticate:
```bash
[ user@local ] gem install travis
[ user@local ] travis login --auto
```

Generate a key without a passphrase to connect `Travis` to the server with `Ansible`.
```bash
[ user@local ] ssh-keygen -t rsa -b 4096 -C 'travis' -f travis_key # generate a key without a passphrase to connect Travis to the server with Ansible
[ user@local ] cat travis_key.pub # display and copy the public key travis_key.pub
```

Add the public key `travis_key.pub` to the server `Ansible` in the file `/home/ansible/.ssh/authorized_keys`.
```bash 
[ ansible@1.1.1.1 ] mcedit /home/ansible/.ssh/authorized_keys
[ ansible@1.1.1.1 ] chmod 600 /home/ansible/.ssh/authorized_keys
```

Encrypt the private key using the `travis` utility:
```bash
[ user@local ] travis encrypt-file travis_key --add
```

The output should be a file `travis_key.enc` and `.travis.yml`. The `.travis.yml` file will contain a line for decrypting our key like this:
```bash
openssl aes-256-cbc -K $encrypted_412afa050e5f_key -iv $encrypted_412afa050e5f_iv -in travis_key.enc -out /tmp/travis_key -d
```

Add both files to `git`:
```bash
[ user@local ] git add travis_key.enc .travis.yml 
```

Edit the file `.travis.yml`:
```bash
[ user@local ] mcedit /path/to/repo/.travis.yml
```

`.travis.yml`
```yaml
language: node_js # node_js project
install: true # do not install additional dependencies
sudo: false
branches: # the deployment of the project will only occur when there are changes in the master branch
  only:
      - master
script:
    - openssl aes-256-cbc -K $encrypted_412afa050e5f_key -iv $encrypted_412afa050e5f_iv -in travis_key.enc -out /tmp/travis_key -d #дешифруем ключ
    - eval "$(ssh-agent -s)"
    - chmod 600 /tmp/travis_key
    - ssh-add /tmp/travis_key
    - ssh -o "StrictHostKeyChecking no" ansible@1.1.1.1 'ansible-playbook playbook.yml' #подключаемся к серверу Ansible и запускаем playbook
```

Push the changes to `git`:
```bash
[ user@local ] git push origin master
```

After this, a `Build` should appear on the repository page in `Travis` similar to the following:
```bash
The command "openssl aes-256-cbc -K $encrypted_412afa050e5f_key -iv $encrypted_412afa050e5f_iv -in travis_key.enc -out /tmp/travis_key -d" exited with 0.

0.01s$ eval "$(ssh-agent -s)"
Agent pid 1842
The command "eval "$(ssh-agent -s)"" exited with 0.

0.01s$ chmod 600 /tmp/travis_key
The command "chmod 600 /tmp/travis_key" exited with 0.

0.01s$ ssh-add /tmp/travis_key
Identity added: /tmp/travis_key (/tmp/travis_key)
The command "ssh-add /tmp/travis_key" exited with 0.

16.68s$ ssh -o "StrictHostKeyChecking no" ansible@2.2.2.2 'ansible-playbook playbook.yml'
Warning: Permanently added '2.2.2.2' (ECDSA) to the list of known hosts.

PLAY [all] *********************************************************************
TASK [setup] *******************************************************************
ok: [2.2.2.2]
TASK [Clone git repo] **********************************************************
changed: [2.2.2.2]
PLAY RECAP *********************************************************************
2.2.2.2               : ok=1   changed=1    unreachable=0    failed=0   
The command "ssh -o "StrictHostKeyChecking no" ansible@2.2.2.2 'ansible-playbook playbook.yml" exited with 0.
Done. Your build exited with 0.
```

Now, whenever changes are made to the master branch, `Travis` will trigger `Ansible`, which will deploy the latest version of your code to the project server.

I hope this guide will be useful to someone.
