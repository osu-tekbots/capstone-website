# OSU Tekbots Web Dev Shared Libraries
Shared PHP scripts and classes among multiple repositories.

All commands are run from the root of the website repository using these shared libraries.

## Adding the shared scripts to your a website repository

```sh
git remote add shared git@github.com:osu-tekbots/lib-shared.git
git subtree add --prefix=lib/shared  --squash shared master
```

## Pulling/Pushing Updates to the Shared Libraries

```sh
# Pull changes
git subtree pull --prefix=lib/shared --squash shared master

# Push changes
git subtree push --prefix=lib/shared --squash shared master
```


> **IMPORTANT**: updates to the shared library are done on the master branch. Only push changes
> to this repository once they have been tested and are deemed applicable to all repositories
> using the shared libraries.

## Resources
The original splitting of the shared libraries from the `osu-tekbots/capstone-website` repository was
accomplished using the following article:

https://makingsoftware.wordpress.com/2013/02/16/using-git-subtrees-for-repository-separation/
