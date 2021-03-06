<?php
/**
 * Author: Falaleev Maxim (max107)
 * Email: <max@studio107.ru>
 * Company: Studio107 <http://studio107.ru>
 * Date: 14/03/17 15:45
 *
 * Инициализация нового subtree
 * php .github/deploy.php init page-bundle
 *
 * Инициализация всех subtree
 * php .github/deploy.php init
 *
 * Создание инициализированного subtree
 * php .github/deploy.php create page-bundle
 *
 * Создание всех инициализированных subtree
 * php .github/deploy.php create
 */
$subtrees = [
    'menu-bundle' => [
        'remote_url' => 'git@github.com:MindyPHP/MenuBundleTest.git',
        'path' => 'src/Mindy/Bundle/MenuBundle',
    ],
];

if (!isset($argv[1])) {
    echo 'Unknown command' . PHP_EOL;
    exit(1);
}

function confirm($question)
{
    while (true) {
        if (PHP_OS == 'WINNT') {
            echo '$> ' . $question . ' [Y/n] ';
            $line = strtolower(trim(stream_get_line(STDIN, 1024, PHP_EOL)));
        } else {
            $line = strtolower(trim(readline('$> ' . $question . ' [Y/n] ')));
        }

        if (!$line || $line == 'y') {
            return true;
        } elseif ($line == 'n') {
            return false;
        }
    }
}

function cmd($command, $dry = false)
{
    echo $command . PHP_EOL;
    if ($dry) {
        return 0;
    }

    $out = [];
    exec($command, $out, $return);
    echo implode("\n", $out) . PHP_EOL;
    return $return;
}

$command = $argv[1];
switch ($command) {
    case 'init':
        if (!isset($argv[2])) {
            if (!confirm('Missing subtree name. You want to init all subtrees?')) {
                return;
            }
            $target = 'all';
        } else {
            $target = $argv[2];
        }

        $commands = [
            'git remote add -f {name} {remote_url}'
        ];
        foreach ($subtrees as $name => $subtree) {
            if ($target == 'all' || $target == $name) {
                foreach ($commands as $command) {
                    cmd(strtr($command, [
                        '{name}' => sprintf("%s-subtree", $name),
                        '{remote_url}' => $subtree['remote_url']
                    ]));
                }
            }
        }
        return;
    case 'create':
        if (!isset($argv[2])) {
            if (!confirm('Missing subtree name. You want to create all subtrees?')) {
                return;
            }
            $target = 'all';
        } else {
            $target = $argv[2];
        }

        $commands = [
            'git rm -rf {path}',
            'git commit -am "clean {name} subtree"',
            'git subtree add --prefix={path} {name} master'
        ];

        $i = 1;
        foreach ($subtrees as $name => $subtree) {
            if ($target == 'all' || $target == $name) {
                echo sprintf("\n\n%s: %s of %s\n\n", $name, $i, count($subtrees));

                foreach ($commands as $command) {
                    cmd(strtr($command, [
                        '{name}' => sprintf("%s-subtree", $name),
                        '{path}' => $subtree['path']
                    ]));
                }
            }

            $i++;
        }
        return;
    case 'push':
        if (!isset($argv[2])) {
            if (!confirm('Missing subtree name. You want to run git push in all subtrees?')) {
                return;
            }
            $target = 'all';
        } else {
            $target = $argv[2];
        }

        $commands = [
            'git subtree push --prefix={path} {name} master',
        ];

        $i = 1;
        foreach ($subtrees as $name => $subtree) {
            if ($target == 'all' || $target == $name) {
                echo sprintf("\n\n%s: %s of %s\n\n", $name, $i, count($subtrees));

                foreach ($commands as $command) {
                    cmd(strtr($command, [
                        '{name}' => sprintf("%s-subtree", $name),
                        '{path}' => $subtree['path']
                    ]));
                }
            }

            $i++;
        }
        return;
    case 'pull':
        if (!isset($argv[2])) {
            if (!confirm('Missing subtree name. You want to run git pull in all subtrees?')) {
                return;
            }
            $target = 'all';
        } else {
            $target = $argv[2];
        }

        $commands = [
            'git subtree pull --prefix={path} {name} master',
        ];

        $i = 1;
        foreach ($subtrees as $name => $subtree) {
            if ($target == 'all' || $target == $name) {
                echo sprintf("\n\n%s: %s of %s\n\n", $name, $i, count($subtrees));

                foreach ($commands as $command) {
                    cmd(strtr($command, [
                        '{name}' => sprintf("%s-subtree", $name),
                        '{path}' => $subtree['path']
                    ]));
                }
            }

            $i++;
        }
        return;
    case 'update_composer_version':
        if (!isset($argv[2])) {
            if (!confirm('Missing subtree name. You want to run git pull in all subtrees?')) {
                return;
            }
            $target = 'all';
        } else {
            $target = $argv[2];
        }

        if (!isset($argv[3])) {
            echo 'Missing tag name' . PHP_EOL;
            exit(1);
        } else {
            $tag = $argv[3];
        }

        echo "Update composer dev-info\n\n";
        foreach ($subtrees as $name => $subtree) {
            if ($target == 'all' || $target == $name) {
                $json = json_decode(file_get_contents($subtree['path']), true);
                $json['extra']['branch-alias']['dev-master'] = sprintf("%s-dev", $tag);
                $newJson = json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
                file_put_contents(sprintf("%s/composer.json", $subtree['path']), $newJson);
            }
        }
        echo "\n\nComposer.json updated in all subtrees. Work directory unclear. Please commit and push changes.\n\n";

        return;
    case 'release':
        if (!isset($argv[2])) {
            if (!confirm('Missing subtree name. You want to run git pull in all subtrees?')) {
                return;
            }
            $target = 'all';
        } else {
            $target = $argv[2];
        }

        if (!isset($argv[3])) {
            echo 'Missing tag name' . PHP_EOL;
            exit(1);
        } else {
            $tag = $argv[3];
        }

        $parentCommands = [
            'git tag -a {tag} -m "Version {tag}"',
            'git push origin refs/tags/{tag}',
        ];
        foreach ($parentCommands as $command) {
            cmd(strtr($command, [
                '{tag}' => $tag
            ]));
        }

        $commands = [
            'git subtree split --prefix={path} -b {temp_name}',
            'git push {name} refs/tags/{tag}',
            'git branch -D {temp_name}',
        ];
        $i = 1;
        foreach ($subtrees as $name => $subtree) {
            if ($target == 'all' || $target == $name) {
                echo sprintf("\n\n%s: %s of %s\n\n", $name, $i, count($subtrees));

                foreach ($commands as $command) {
                    cmd(strtr($command, [
                        '{name}' => sprintf("%s-subtree", $name),
                        '{temp_name}' => sprintf("%s-subtree-%s", $name, $tag),
                        '{path}' => $subtree['path'],
                        '{tag}' => $tag
                    ]));
                }
            }
            $i++;
        }

        $parentCommands = [
            'git gc --prune=now'
        ];
        foreach ($parentCommands as $command) {
            cmd(strtr($command, [
                '{tag}' => $tag
            ]));
        }

        return;
    case 'update_doc':
        $readme = <<<TXT
# {title}

[![Build Status](https://travis-ci.org/MindyPHP/{travis_name}.svg?branch=master)](https://travis-ci.org/MindyPHP/{travis_name})
[![Coverage Status](https://img.shields.io/coveralls/MindyPHP/{travis_name}.svg)](https://coveralls.io/r/MindyPHP/{travis_name})
[![Latest Stable Version](https://poser.pugx.org/mindy/{name}/v/stable.svg)](https://packagist.org/packages/mindy/{name})
[![Total Downloads](https://poser.pugx.org/mindy/{name}/downloads.svg)](https://packagist.org/packages/mindy/{name})

The {title}

Resources
---------

  * [Documentation](https://mindy-cms.com/doc/current/{url_part}/index.html)
  * [Contributing](https://mindy-cms.com/doc/current/contributing/index.html)
  * [Report issues](https://github.com/MindyPHP/mindy/issues) and
    [send Pull Requests](https://github.com/MindyPHP/mindy/pulls)
    in the [main Mindy repository](https://github.com/MindyPHP/mindy)

![yandex](https://mc.yandex.ru/watch/43423684 "yandex")
TXT;
        $license = <<<TXT
Copyright (c) 2010-{year} the Maxim Falaleev
All rights reserved.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

TXT;

        if (!isset($argv[2])) {
            if (!confirm('Missing subtree name. You want to update readme in all subtrees?')) {
                return;
            }
            $target = 'all';
        } else {
            $target = $argv[2];
        }

        foreach ($subtrees as $name => $subtree) {
            if ($target == 'all' || $target == $name) {
                $title = implode(' ', array_map('ucfirst', explode(' ', str_replace('-', ' ', $name))));
                $travisName = str_replace(' ', '', $title);
                if (strpos($title, 'Bundle') === false) {
                    $title .= ' Component';
                }

                if (strpos($name, 'bundle') === false) {
                    $urlPart = sprintf('components/%s', $name);
                } else {
                    $urlPart = sprintf('bundles/%s', str_replace('-bundle', '', $name));
                }

                $patterns = [
                    '%s/README.*',
                    '%s/readme.*',
                    '%s/readme',
                    '%s/README',
                    '%s/LICENSE.*',
                    '%s/LICENSE',
                    '%s/license.*',
                    '%s/license',
                ];
                foreach ($patterns as $pattern) {
                    array_map('unlink', glob(sprintf($pattern, $subtree['path'])));
                }

                $readmeDoc = strtr($readme, [
                    '{url_part}' => $urlPart,
                    '{travis_name}' => $travisName,
                    '{title}' => $title,
                    '{name}' => $name
                ]);
                file_put_contents(sprintf('%s/README.md', $subtree['path']), $readmeDoc);

                $licenseDoc = strtr($license, [
                    '{year}' => date('Y'),
                ]);
                file_put_contents(sprintf('%s/LICENSE', $subtree['path']), $licenseDoc);
                file_put_contents(sprintf('%s/.php_cs', $subtree['path']), file_get_contents(__DIR__.'/.php_cs'));
            }
        }
        return;
}
