#!/usr/bin/env bash

#  1. Clone complete SVN repository to separate directory
svn co $WORDPRESS_ORG_REPO ../svn

#  2. Copy git repository contents to SNV trunk/ directory
echo "2. Copy git:"
ls 
echo "2.0 ./*"
ls ./*
cp -R ./* ../svn/trunk/
echo "2.1 svn/trunk:"
ls ../svn/trunk/

#  3. Go to trunk/
cd ../svn/trunk/

#  4. Move assets/ to SVN /assets/
mv ./assets/ ../assets/

#  5. Delete .git/
rm -rf .git/

#  6. Delete deploy/
rm -rf deploy/

#  7. Delete .travis.yml
rm .travis.yml

# 8. Go to SVN home directory && copy trunk/ to tags/{tag}/
cd ../
svn cp trunk tags/$TRAVIS_TAG

# 9. Commit SVN tag
svn ci  --message "Release $TRAVIS_TAG" \
        --username $WORDPRESS_ORG_USERNAME \
        --password $WORDPRESS_ORG_PASSWORD \
        --non-interactive
