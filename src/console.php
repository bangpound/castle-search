<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

$console = new Application('Castle Search UI', '0.0.1');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));

$app->boot();

$console
    ->register('assetic:dump')
    ->setDescription('Dumps all assets to the filesystem')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        if (!$app['assetic.enabled']) {
            return false;
        }

        $dumper = $app['assetic.dumper'];
        if (isset($app['twig'])) {
            $dumper->addTwigAssets();
        }
        $dumper->dumpAssets();
        $output->writeln('<info>Dump finished</info>');
    })
;

if (isset($app['cache.path'])) {
    $console
        ->register('cache:clear')
        ->setDescription('Clears the cache')
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {

            $cacheDir = $app['cache.path'];
            $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');

            $filesystem = new Filesystem();
            $filesystem->remove($finder);

            $output->writeln(sprintf("%s <info>success</info>", 'cache:clear'));
        });
}

$console
    ->register('assets:install')
    ->setDefinition(array(
        new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
    ))
    ->addOption('symlink', null, InputOption::VALUE_NONE, 'Symlinks the assets instead of copying it')
    ->addOption('relative', null, InputOption::VALUE_NONE, 'Make relative symlinks')
    ->setDescription('Installs bundles web assets under a public web directory')
    ->setHelp(<<<EOT
The <info>%command.name%</info> command installs bundle assets into a given
directory (e.g. the web directory).

<info>php %command.full_name% web</info>

A "bundles" directory will be created inside the target directory, and the
"Resources/public" directory of each bundle will be copied into it.

To create a symlink to each bundle instead of copying its assets, use the
<info>--symlink</info> option:

<info>php %command.full_name% web --symlink</info>

To make symlink relative, add the <info>--relative</info> option:

<info>php %command.full_name% web --symlink --relative</info>

EOT
    )
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $targetArg = rtrim($input->getArgument('target'), '/');

        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        if (!function_exists('symlink') && $input->getOption('symlink')) {
            throw new \InvalidArgumentException('The symlink() function is not available on your system. You need to install the assets without the --symlink option.');
        }

        $filesystem = new Filesystem();

        // Create the bundles directory otherwise symlink will fail.
        $filesystem->mkdir($targetArg, 0777);

        $output->writeln(sprintf("Installing assets using the <comment>%s</comment> option", $input->getOption('symlink') ? 'symlink' : 'hard copy'));

        $assetDir = $app['assetic.path_to_source'];

        if (is_dir($originDir = $assetDir)) {
            $targetDir  = $targetArg;

            $output->writeln(sprintf('Installing assets into <comment>%s</comment>', $targetDir));

            $filesystem->remove($targetDir);

            if ($input->getOption('symlink')) {
                if ($input->getOption('relative')) {
                    $relativeOriginDir = $filesystem->makePathRelative($originDir, realpath($targetDir));
                } else {
                    $relativeOriginDir = $originDir;
                }
                $filesystem->symlink($relativeOriginDir, $targetDir);
            } else {
                $filesystem->mkdir($targetDir, 0777);
                // We use a custom iterator to ignore VCS files
                $filesystem->mirror($originDir, $targetDir, Finder::create()->in($originDir));
            }
        }
    })
;

return $console;
