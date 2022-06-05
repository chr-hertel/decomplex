<?php

namespace Deployer;

require 'recipe/symfony.php';

// Config
set('repository', 'git@github.com:chr-hertel/decomplex.git');
set('composer_options', '--no-dev --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction --no-scripts');
set('console_options', '--no-interaction --env=prod');

// Hosts
host('decomplex.me')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '/var/www/decomplex');

// Tasks
task('build', function () {
    cd('{{release_path}}');
    run('npm clean-install');
    run('npm run build');
});

after('deploy:cache:clear', 'build');
after('deploy:failed', 'deploy:unlock');
