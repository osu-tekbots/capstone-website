# OSU Tekbots Web Dev Shared Client Assets
Shared JavaScript and CSS files among multiple repositories.

All commands are run from the root of the website repository using these shared libraries.

## Adding the shared scripts to a website repository

```sh
git remote add shared-assets git@github.com:osu-tekbots/assets-shared.git
git subtree add --prefix=assets/shared  --squash shared-assets master
```

## Pulling/Pushing Updates to the Shared Assets

```sh
# Pull changes
git subtree pull --prefix=assets/shared --squash shared-assets master

# Push changes
git subtree push --prefix=assets/shared --squash shared-assets master
```

> **IMPORTANT**: updates to the shared assets are done on the master branch. Only push changes
> to this repository once they have been tested and are deemed applicable to all repositories
> using the shared assets.
