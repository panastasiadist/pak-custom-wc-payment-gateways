#!/bin/bash

# The name of the final package which will contain the plugin's file.
package_name="pak-custom-wc-payment-gateways"

# The relative filesystem path to the build directory.
build_path="build"

# The relative filesystem path to the branding directory.
branding_path="branding"

# The relative filesystem path to the publish (SVN) directory.
publish_path="publish"

# The relative filesystem path to the directory containing the plugin's production files.
package_path="$build_path/$package_name"

# The relative filesystem path to the readme.txt file containing the Stable tag header field.
readme_path="readme.txt"

# Store the line containing the Stable tag header field.
line=$(grep 'Stable tag' "$readme_path")

# Extract the version number by removing everything before the :
stable_tag=${line#*:}

# Remove leading whitespace characters
stable_tag="${stable_tag#"${stable_tag%%[![:space:]]*}"}"

echo "Package name: $package_name"
echo "Build directory: $build_path"
echo "Package directory: $package_path"
echo "Publish directory: $publish_path"
echo "Readme path: $readme_path"
echo "Stable tag: $stable_tag"

if [ -d $publish_path ]; then
  echo "Publish directory already exists. Updating it from the repository..."
  cd "$publish_path" || { echo "Failed to enter publish directory."; exit; }
  svn update
else
  mkdir -p "$publish_path"
  echo "Publish directory created. Checking our the repository..."
  cd "$publish_path" || { echo "Failed to enter publish directory."; exit; }
  svn checkout "https://plugins.svn.wordpress.org/$package_name" ./
fi

# Create the tag directory in case it doesn't already exist.
if [[ -d $publish_path ]]; then
  mkdir -p "tags/$stable_tag" || { echo "Failed to create the tags/$stable_tag."; exit; }
  echo "Created new tags/$stable_tag directory."
fi

# Sync everything in the package directory to the repository directory.
echo "Syncing the package directory to the repository directory..."
rsync -rc --delete "../$branding_path/" "assets"
rsync -rc --delete "../$package_path/" "trunk"
rsync -rc --delete "../$package_path/" "tags/$stable_tag"

# Recursively add everything under the publish (SVN) directory to the repository, ignoring any already versioned files.
svn add --force ./*

echo "Everything new or modified added to the repository."
echo "Deleting non-existent files from the repository..."

# Function to check if a local directory exists
directory_exists() {
    if [ -d "$1" ]; then return 0; else return 1; fi
}

# Function to check if a local file exists
file_exists() {
    if [ -f "$1" ]; then return 0; else return 1; fi
}

# Iterate over each file in the remote repository and find out if it still exists within the local copy.
svn list -R | while read -r repository_relative_path
do
    if [[ ${repository_relative_path: -1} == '/' ]]; then
      # This is a directory.
      # Remove trailing slash
      local_relative_directory_path=${repository_relative_path: : -1}

      directory_exists "$local_relative_directory_path"
      local_path_exists=$?

      if (( local_path_exists == 1 )); then
          svn delete "$local_relative_directory_path" -m "Removed directory $local_relative_directory_path as it has been deleted from the release package."
      fi
    else
      # This is a file.
      file_exists "$repository_relative_path"
      local_path_exists=$?

      if (( local_path_exists == 1 )); then
          svn delete "$repository_relative_path" -m "Removed file $repository_relative_path as it has been deleted from the release package."
      fi
    fi
done

echo "Deleted non-existent files from the repository."
echo "Committing local changes..."

svn commit -m "Released version $stable_tag"

echo "Complete!"
