#!/bin/bash

# The name of the final package which will contain the plugin's file.
package_name="pak-custom-wc-payment-gateways"

# The relative filesystem path to the build directory.
build_path="build"

# The relative filesystem path to the directory containing the plugin's production files.
package_path="$build_path/$package_name"

echo "Package name: $package_name"
echo "Build directory: $build_path"
echo "Package directory: $package_path"

# Delete the build directory if it already exists, in order to start on a clean basis.
if [[ -d $build_path ]]; then
  echo "Build directory already exists. Deleting it..."
  rm -rf "$build_path"
fi

# Create the directory that will hold the plugin's production files.
mkdir -p "$package_path"
echo "Package directory created."

rsync \
  -r \
  --exclude=".*" \
  --exclude="*.sh" \
  --exclude="*.md" \
  --exclude="vendor" \
  --exclude="branding" \
  --exclude="build" \
  --exclude="publish" \
  --exclude="admin/settings/node_modules" \
  ./ "$package_path"

echo "Source files copied to build directory and ready for further processing."

# Enter the package path to further process its files.
cd "$package_path" || { echo "Failed to enter the package directory."; exit; }

# Execute "composer dump-autoload -a" to produce an optimized Composer bundle
composer install
composer dump-autoload -a
echo "Composer optimized build produced."

# Delete non-production files within the directory
find ./languages -type f ! -name '*.mo' ! -name '*.po' ! -name '*.pot' -delete
echo "Superfluous files deleted."

# Enter the "admin/settings" directory which hosts a Node-based project.
cd admin/settings || { echo "Failed to enter the admin/settings directory."; exit; }

# Execute "npm run build" to produce a bundle out of the contained Node-based project.
npm install
npm run build
echo "admin/settings project built."

# Delete non-production files within the Node-based project.
find . -type f ! -path './dist/*' -delete

# Delete non-production files with the "dist" directory of the Node-based project.
find . -type f -path './dist/assets/*.map' -delete

# Delete non-production directories within the Node-based project.
find . -mindepth 1 -type d ! -path './dist' ! -path './dist/*' -exec rm -rf {} +

echo "Non-production source files removed."

# We are in this location SCRIPT_DIRECTORY/BUILD_DIRECTORY/PACKAGE_NAME/admin/settings
# Navigate a few levels up to enter the build directory.
cd ../../../

# Create the final zip bundle out of the package directory. This file should be ready to be installed on a WordPress website.
zip -r "./$package_name.zip" "./$package_name"
echo "Final $package_name.zip package created."

echo "Complete!"
