Using stable releases
=====================

Symfony Standard Edition is delivered by default with a composer.json
file that will fetch the latest dev release of Symfony2 and the
suggested bundles for this edition.


## Locking in stable release

Should you want to lock in as much stable releases of Symfony2 and the
bundles defined in this edition, replace your composer.json with the
content of the composer.json file within this folder.


## Adding a bundle that does not have a stable release yet

Some bundles you will want to add might not have a stable release available
yet and may instruct you to add a composer entry that uses a "x.x.*" version
number without explicitly pointing to their "dev" release. If composer cannot
resolve its dependencies for that bundle, try appending `@dev` to its version
value.


Example:

    "require" {
        ...
        "kriswallsmith/assetic": "1.1.*"
    }


Would become:

    "require" {
        ...
        "kriswallsmith/assetic": "1.1.*@dev"
    }
