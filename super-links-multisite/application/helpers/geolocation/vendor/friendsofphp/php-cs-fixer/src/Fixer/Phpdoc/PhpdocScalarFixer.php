<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractPhpdocTypesFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class PhpdocScalarFixer extends AbstractPhpdocTypesFixer
{
    /**
     * The types to fix.
     *
     * @var array
     */
    private static $types = [
        'boolean' => 'bool',
        'double' => 'float',
        'integer' => 'int',
        'real' => 'float',
        'str' => 'string',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Scalar types should always be written in the same form. `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.',
            [new CodeSample('<?php
/**
 * @param integer $a
 * @param boolean $b
 * @param real $c
 *
 * @return double
 */
function sample($a, $b, $c)
{
    return sample2($a, $b, $c);
}
')]
        );
    }

    public function getPriority()
    {
        /*
         * Should be run before all other docblock fixers apart from the
         * phpdoc_to_comment and phpdoc_indent fixer to make sure all fixers
         * apply correct indentation to new code they add. This should run
         * before alignment of params is done since this fixer might change
         * the type and thereby un-aligning the params. We also must run after
         * the phpdoc_types_fixer because it can convert types to things that
         * we can fix.
         */
        return 15;
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize($type)
    {
        if (array_key_exists($type, self::$types)) {
            return self::$types[$type];
        }

        return $type;
    }
}
