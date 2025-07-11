PHP Coding Standards Fixer
==========================

The PHP Coding Standards Fixer (PHP CS Fixer) tool fixes your code to follow standards;
whether you want to follow PHP coding standards as defined in the PSR-1, PSR-2, etc.,
or other community driven ones like the Symfony one.
You can **also** define your (teams) style through configuration.

It can modernize your code (like converting the `pow` function to the `**` operator on PHP 5.6)
and (micro) optimize it.

If you are already using a linter to identify coding standards problems in your
code, you know that fixing them by hand is tedious, especially on large
projects. This tool does not only detect them, but also fixes them for you.

The PHP CS Fixer is maintained on GitHub at https://github.com/FriendsOfPHP/PHP-CS-Fixer
bug reports and ideas about new features are welcome there.

You can talk to us at https://gitter.im/PHP-CS-Fixer/Lobby about the project,
configuration, possible improvements, ideas and questions, please visit us!

Requirements
------------

PHP needs to be a minimum version of PHP 5.6.0.

Installation
------------

Locally
~~~~~~~

Download the `php-cs-fixer.phar`_ file and store it somewhere on your computer.

Globally (manual)
~~~~~~~~~~~~~~~~~

You can run these commands to easily access latest ``php-cs-fixer`` from anywhere on
your system:

.. code-block:: bash

    $ wget http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -O php-cs-fixer

or with specified version:

.. code-block:: bash

    $ wget https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v2.10.0/php-cs-fixer.phar -O php-cs-fixer

or with curl:

.. code-block:: bash

    $ curl -L http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -o php-cs-fixer

then:

.. code-block:: bash

    $ sudo chmod a+x php-cs-fixer
    $ sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer

Then, just run ``php-cs-fixer``.

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

To install PHP CS Fixer, `install Composer <https://getcomposer.org/download/>`_ and issue the following command:

.. code-block:: bash

    $ composer global require friendsofphp/php-cs-fixer

Then make sure you have the global Composer binaries directory in your ``PATH``. This directory is platform-dependent, see `Composer documentation <https://getcomposer.org/doc/03-cli.md#composer-home>`_ for details. Example for some Unix systems:

.. code-block:: bash

    $ export PATH="$PATH:$HOME/.composer/vendor/bin"

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

PHP-CS-Fixer is part of the homebrew-php project. Follow the installation
instructions at https://github.com/homebrew/homebrew-php if you don't
already have it.

.. code-block:: bash

    $ brew install homebrew/php/php-cs-fixer

Update
------

Locally
~~~~~~~

The ``self-update`` command tries to update ``php-cs-fixer`` itself:

.. code-block:: bash

    $ php php-cs-fixer.phar self-update

Globally (manual)
~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    $ sudo php-cs-fixer self-update

Globally (Composer)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    $ ./composer.phar global update friendsofphp/php-cs-fixer

Globally (homebrew)
~~~~~~~~~~~~~~~~~~~

You can update ``php-cs-fixer`` through this command:

.. code-block:: bash

    $ brew upgrade php-cs-fixer

Usage
-----

The ``fix`` command tries to fix as much coding standards
problems as possible on a given file or files in a given directory and its subdirectories:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/dir
    $ php php-cs-fixer.phar fix /path/to/file

By default ``--path-mode`` is set to ``override``, which means, that if you specify the path to a file or a directory via
command arguments, then the paths provided to a ``Finder`` in config file will be ignored. You can use ``--path-mode=intersection``
to merge paths from the config file and from the argument:

.. code-block:: bash

    $ php php-cs-fixer.phar fix --path-mode=intersection /path/to/dir

The ``--format`` option for the output format. Supported formats are ``txt`` (default one), ``json``, ``xml``, ``checkstyle`` and ``junit``.

NOTE: the output for the following formats are generated in accordance with XML schemas

* ``junit`` follows the `JUnit xml schema from Jenkins </doc/junit-10.xsd>`_
* ``checkstyle`` follows the common `"checkstyle" xml schema </doc/checkstyle.xsd>`_


The ``--verbose`` option will show the applied rules. When using the ``txt`` format it will also displays progress notifications.

The ``--rules`` option limits the rules to apply on the
project:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/project --rules=@PSR2

By default the PSR1 and PSR2 rules are used.

The ``--rules`` option lets you choose the exact rules to
apply (the rule names must be separated by a comma):

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/dir --rules=line_ending,full_opening_tag,indentation_type

You can also blacklist the rules you don't want by placing a dash in front of the rule name, if this is more convenient,
using ``-name_of_fixer``:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/dir --rules=-full_opening_tag,-indentation_type

When using combinations of exact and blacklist rules, applying exact rules along with above blacklisted results:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/project --rules=@Symfony,-@PSR1,-blank_line_before_statement,strict_comparison

Complete configuration for rules can be supplied using a ``json`` formatted string.

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/project --rules='{"concat_space": {"spacing": "none"}}'

The ``--dry-run`` flag will run the fixer without making changes to your files.

The ``--diff`` flag can be used to let the fixer output all the changes it makes.

The ``--diff-format`` option allows to specify in which format the fixer should output the changes it makes:

* ``udiff``: unified diff format;
* ``sbd``: Sebastianbergmann/diff format (default when using `--diff` without specifying `diff-format`).

The ``--allow-risky`` option (pass ``yes`` or ``no``) allows you to set whether risky rules may run. Default value is taken from config file.
Risky rule is a rule, which could change code behaviour. By default no risky rules are run.

The ``--stop-on-violation`` flag stops the execution upon first file that needs to be fixed.

The ``--show-progress`` option allows you to choose the way process progress is rendered:

* ``none``: disables progress output;
* ``run-in``: [deprecated] simple single-line progress output;
* ``estimating``: [deprecated] multiline progress output with number of files and percentage on each line. Note that with this option, the files list is evaluated before processing to get the total number of files and then kept in memory to avoid using the file iterator twice. This has an impact on memory usage so using this option is not recommended on very large projects;
* ``estimating-max``: [deprecated] same as ``dots``;
* ``dots``: same as ``estimating`` but using all terminal columns instead of default 80.

If the option is not provided, it defaults to ``run-in`` unless a config file that disables output is used, in which case it defaults to ``none``. This option has no effect if the verbosity of the command is less than ``verbose``.

.. code-block:: bash

    $ php php-cs-fixer.phar fix --verbose --show-progress=estimating

The command can also read from standard input, in which case it won't
automatically fix anything:

.. code-block:: bash

    $ cat foo.php | php php-cs-fixer.phar fix --diff -

Finally, if you don't need BC kept on CLI level, you might use `PHP_CS_FIXER_FUTURE_MODE` to start using options that
would be default in next MAJOR release (unified differ, estimating, full-width progress indicator):

.. code-block:: bash

    $ PHP_CS_FIXER_FUTURE_MODE=1 php php-cs-fixer.phar fix -v --diff

Choose from the list of available rules:

* **align_multiline_comment**

  Each line of multi-line DocComments must have an asterisk [PSR-5] and
  must be aligned with the first one.

  Configuration options:

  - ``comment_type`` (``'all_multiline'``, ``'phpdocs_like'``, ``'phpdocs_only'``): whether
    to fix PHPDoc comments only (``phpdocs_only``), any multi-line comment
    whose lines all start with an asterisk (``phpdocs_like``) or any
    multi-line comment (``all_multiline``); defaults to ``'phpdocs_only'``

* **array_syntax**

  PHP arrays should be declared using the configured syntax.

  Configuration options:

  - ``syntax`` (``'long'``, ``'short'``): whether to use the ``long`` or ``short`` array
    syntax; defaults to ``'long'``

* **backtick_to_shell_exec**

  Converts backtick operators to shell_exec calls.

* **binary_operator_spaces** [@Symfony]

  Binary operators should be surrounded by space as configured.

  Configuration options:

  - ``align_double_arrow`` (``false``, ``null``, ``true``): (deprecated) Whether to apply,
    remove or ignore double arrows alignment; defaults to ``false``
  - ``align_equals`` (``false``, ``null``, ``true``): (deprecated) Whether to apply, remove
    or ignore equals alignment; defaults to ``false``
  - ``default`` (``'align'``, ``'align_single_space'``, ``'align_single_space_minimal'``,
    ``'single_space'``, ``null``): default fix strategy; defaults to ``'single_space'``
  - ``operators`` (``array``): dictionary of ``binary operator`` => ``fix strategy``
    values that differ from the default strategy; defaults to ``[]``

* **blank_line_after_namespace** [@PSR2, @Symfony]

  There MUST be one blank line after the namespace declaration.

* **blank_line_after_opening_tag** [@Symfony]

  Ensure there is no code on the same line as the PHP open tag and it is
  followed by a blank line.

* **blank_line_before_return**

  An empty line feed should precede a return statement. DEPRECATED: use
  ``blank_line_before_statement`` instead.

* **blank_line_before_statement** [@Symfony]

  An empty line feed must precede any configured statement.

  Configuration options:

  - ``statements`` (``array``): list of statements which must be preceded by an
    empty line; defaults to ``['break', 'continue', 'declare', 'return',
    'throw', 'try']``

* **braces** [@PSR2, @Symfony]

  The body of each structure MUST be enclosed by braces. Braces should be
  properly placed. Body of braces should be properly indented.

  Configuration options:

  - ``allow_single_line_closure`` (``bool``): whether single line lambda notation
    should be allowed; defaults to ``false``
  - ``position_after_anonymous_constructs`` (``'next'``, ``'same'``): whether the
    opening brace should be placed on "next" or "same" line after anonymous
    constructs (anonymous classes and lambda functions); defaults to ``'same'``
  - ``position_after_control_structures`` (``'next'``, ``'same'``): whether the opening
    brace should be placed on "next" or "same" line after control
    structures; defaults to ``'same'``
  - ``position_after_functions_and_oop_constructs`` (``'next'``, ``'same'``): whether
    the opening brace should be placed on "next" or "same" line after
    classy constructs (non-anonymous classes, interfaces, traits, methods
    and non-lambda functions); defaults to ``'next'``

* **cast_spaces** [@Symfony]

  A single space or none should be between cast and variable.

  Configuration options:

  - ``space`` (``'none'``, ``'single'``): spacing to apply between cast and variable;
    defaults to ``'single'``

* **class_attributes_separation** [@Symfony]

  Class, trait and interface elements must be separated with one blank
  line.

  Configuration options:

  - ``elements`` (``array``): list of classy elements; 'const', 'method',
    'property'; defaults to ``['const', 'method', 'property']``

* **class_definition** [@PSR2, @Symfony]

  Whitespace around the keywords of a class, trait or interfaces
  definition should be one space.

  Configuration options:

  - ``multiLineExtendsEachSingleLine`` (``bool``): whether definitions should be
    multiline; defaults to ``false``
  - ``singleItemSingleLine`` (``bool``): whether definitions should be single line
    when including a single item; defaults to ``false``
  - ``singleLine`` (``bool``): whether definitions should be single line; defaults
    to ``false``

* **class_keyword_remove**

  Converts ``::class`` keywords to FQCN strings.

* **combine_consecutive_issets**

  Using ``isset($var) &&`` multiple times should be done in one call.

* **combine_consecutive_unsets**

  Calling ``unset`` on multiple items should be done in one call.

* **compact_nullable_typehint**

  Remove extra spaces in a nullable typehint.

* **concat_space** [@Symfony]

  Concatenation should be spaced according configuration.

  Configuration options:

  - ``spacing`` (``'none'``, ``'one'``): spacing to apply around concatenation operator;
    defaults to ``'none'``

* **declare_equal_normalize** [@Symfony]

  Equal sign in declare statement should be surrounded by spaces or not
  following configuration.

  Configuration options:

  - ``space`` (``'none'``, ``'single'``): spacing to apply around the equal sign;
    defaults to ``'none'``

* **declare_strict_types** [@PHP70Migration:risky, @PHP71Migration:risky]

  Force strict types declaration in all files. Requires PHP >= 7.0.

  *Risky rule: forcing strict types will stop non strict code from working.*

* **dir_constant** [@Symfony:risky]

  Replaces ``dirname(__FILE__)`` expression with equivalent ``__DIR__``
  constant.

  *Risky rule: risky when the function ``dirname`` is overridden.*

* **doctrine_annotation_array_assignment** [@DoctrineAnnotation]

  Doctrine annotations must use configured operator for assignment in
  arrays.

  Configuration options:

  - ``ignored_tags`` (``array``): list of tags that must not be treated as Doctrine
    Annotations; defaults to ``['abstract', 'access', 'code', 'deprec',
    'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc',
    'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar',
    'staticVar', 'throw', 'api', 'author', 'category', 'copyright',
    'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal',
    'license', 'link', 'method', 'package', 'param', 'property',
    'property-read', 'property-write', 'return', 'see', 'since', 'source',
    'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var',
    'version', 'after', 'afterClass', 'backupGlobals',
    'backupStaticAttributes', 'before', 'beforeClass',
    'codeCoverageIgnore', 'codeCoverageIgnoreStart',
    'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass',
    'coversNothing', 'dataProvider', 'depends', 'expectedException',
    'expectedExceptionCode', 'expectedExceptionMessage',
    'expectedExceptionMessageRegExp', 'group', 'large', 'medium',
    'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses',
    'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses',
    'SuppressWarnings', 'noinspection', 'package_version', 'enduml',
    'startuml', 'fix', 'FIXME', 'fixme', 'override']``
  - ``operator`` (``':'``, ``'='``): the operator to use; defaults to ``'='``

* **doctrine_annotation_braces** [@DoctrineAnnotation]

  Doctrine annotations without arguments must use the configured syntax.

  Configuration options:

  - ``ignored_tags`` (``array``): list of tags that must not be treated as Doctrine
    Annotations; defaults to ``['abstract', 'access', 'code', 'deprec',
    'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc',
    'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar',
    'staticVar', 'throw', 'api', 'author', 'category', 'copyright',
    'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal',
    'license', 'link', 'method', 'package', 'param', 'property',
    'property-read', 'property-write', 'return', 'see', 'since', 'source',
    'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var',
    'version', 'after', 'afterClass', 'backupGlobals',
    'backupStaticAttributes', 'before', 'beforeClass',
    'codeCoverageIgnore', 'codeCoverageIgnoreStart',
    'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass',
    'coversNothing', 'dataProvider', 'depends', 'expectedException',
    'expectedExceptionCode', 'expectedExceptionMessage',
    'expectedExceptionMessageRegExp', 'group', 'large', 'medium',
    'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses',
    'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses',
    'SuppressWarnings', 'noinspection', 'package_version', 'enduml',
    'startuml', 'fix', 'FIXME', 'fixme', 'override']``
  - ``syntax`` (``'with_braces'``, ``'without_braces'``): whether to add or remove
    braces; defaults to ``'without_braces'``

* **doctrine_annotation_indentation** [@DoctrineAnnotation]

  Doctrine annotations must be indented with four spaces.

  Configuration options:

  - ``ignored_tags`` (``array``): list of tags that must not be treated as Doctrine
    Annotations; defaults to ``['abstract', 'access', 'code', 'deprec',
    'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc',
    'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar',
    'staticVar', 'throw', 'api', 'author', 'category', 'copyright',
    'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal',
    'license', 'link', 'method', 'package', 'param', 'property',
    'property-read', 'property-write', 'return', 'see', 'since', 'source',
    'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var',
    'version', 'after', 'afterClass', 'backupGlobals',
    'backupStaticAttributes', 'before', 'beforeClass',
    'codeCoverageIgnore', 'codeCoverageIgnoreStart',
    'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass',
    'coversNothing', 'dataProvider', 'depends', 'expectedException',
    'expectedExceptionCode', 'expectedExceptionMessage',
    'expectedExceptionMessageRegExp', 'group', 'large', 'medium',
    'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses',
    'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses',
    'SuppressWarnings', 'noinspection', 'package_version', 'enduml',
    'startuml', 'fix', 'FIXME', 'fixme', 'override']``
  - ``indent_mixed_lines`` (``bool``): whether to indent lines that have content
    before closing parenthesis; defaults to ``false``

* **doctrine_annotation_spaces** [@DoctrineAnnotation]

  Fixes spaces in Doctrine annotations.

  Configuration options:

  - ``after_argument_assignments`` (``null``, ``bool``): whether to add, remove or
    ignore spaces after argument assignment operator; defaults to ``false``
  - ``after_array_assignments_colon`` (``null``, ``bool``): whether to add, remove or
    ignore spaces after array assignment ``:`` operator; defaults to ``true``
  - ``after_array_assignments_equals`` (``null``, ``bool``): whether to add, remove or
    ignore spaces after array assignment ``=`` operator; defaults to ``true``
  - ``around_argument_assignments`` (``bool``): whether to fix spaces around
    argument assignment operator (deprecated, use
    ``before_argument_assignments`` and ``after_argument_assignments`` options
    instead); defaults to ``true``
  - ``around_array_assignments`` (``bool``): whether to fix spaces around array
    assignment operators (deprecated, use ``before_array_assignments_*`` and
    ``after_array_assignments_*`` options instead); defaults to ``true``
  - ``around_commas`` (``bool``): whether to fix spaces around commas; defaults to
    ``true``
  - ``around_parentheses`` (``bool``): whether to fix spaces around parentheses;
    defaults to ``true``
  - ``before_argument_assignments`` (``null``, ``bool``): whether to add, remove or
    ignore spaces before argument assignment operator; defaults to ``false``
  - ``before_array_assignments_colon`` (``null``, ``bool``): whether to add, remove or
    ignore spaces before array ``:`` assignment operator; defaults to ``true``
  - ``before_array_assignments_equals`` (``null``, ``bool``): whether to add, remove or
    ignore spaces before array ``=`` assignment operator; defaults to ``true``
  - ``ignored_tags`` (``array``): list of tags that must not be treated as Doctrine
    Annotations; defaults to ``['abstract', 'access', 'code', 'deprec',
    'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc',
    'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar',
    'staticVar', 'throw', 'api', 'author', 'category', 'copyright',
    'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal',
    'license', 'link', 'method', 'package', 'param', 'property',
    'property-read', 'property-write', 'return', 'see', 'since', 'source',
    'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var',
    'version', 'after', 'afterClass', 'backupGlobals',
    'backupStaticAttributes', 'before', 'beforeClass',
    'codeCoverageIgnore', 'codeCoverageIgnoreStart',
    'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass',
    'coversNothing', 'dataProvider', 'depends', 'expectedException',
    'expectedExceptionCode', 'expectedExceptionMessage',
    'expectedExceptionMessageRegExp', 'group', 'large', 'medium',
    'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses',
    'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses',
    'SuppressWarnings', 'noinspection', 'package_version', 'enduml',
    'startuml', 'fix', 'FIXME', 'fixme', 'override']``

* **elseif** [@PSR2, @Symfony]

  The keyword ``elseif`` should be used instead of ``else if`` so that all
  control keywords look like single words.

* **encoding** [@PSR1, @PSR2, @Symfony]

  PHP code MUST use only UTF-8 without BOM (remove BOM).

* **ereg_to_preg** [@Symfony:risky]

  Replace deprecated ``ereg`` regular expression functions with preg.

  *Risky rule: risky if the ``ereg`` function is overridden.*

* **escape_implicit_backslashes**

  Escape implicit backslashes in strings and heredocs to ease the
  understanding of which are special chars interpreted by PHP and which
  not.

  Configuration options:

  - ``double_quoted`` (``bool``): whether to fix double-quoted strings; defaults to
    ``true``
  - ``heredoc_syntax`` (``bool``): whether to fix heredoc syntax; defaults to ``true``
  - ``single_quoted`` (``bool``): whether to fix single-quoted strings; defaults to
    ``false``

* **explicit_indirect_variable**

  Add curly braces to indirect variables to make them clear to understand.
  Requires PHP >= 7.0.

* **explicit_string_variable**

  Converts implicit variables into explicit ones in double-quoted strings
  or heredoc syntax.

* **final_internal_class**

  Internal classes should be ``final``.

  *Risky rule: changing classes to ``final`` might cause code execution to break.*

  Configuration options:

  - ``annotation-black-list`` (``array``): class level annotations tags that must be
    omitted to fix the class, even if all of the white list ones are used
    as well. (case insensitive); defaults to ``['@final', '@Entity', '@ORM']``
  - ``annotation-white-list`` (``array``): class level annotations tags that must be
    set in order to fix the class. (case insensitive); defaults to
    ``['@internal']``

* **full_opening_tag** [@PSR1, @PSR2, @Symfony]

  PHP code must use the long ``<?php`` tags or short-echo ``<?=`` tags and not
  other tag variations.

* **function_declaration** [@PSR2, @Symfony]

  Spaces should be properly placed in a function declaration.

  Configuration options:

  - ``closure_function_spacing`` (``'none'``, ``'one'``): spacing to use before open
    parenthesis for closures; defaults to ``'one'``

* **function_to_constant** [@Symfony:risky]

  Replace core functions calls returning constants with the constants.

  *Risky rule: risky when any of the configured functions to replace are overridden.*

  Configuration options:

  - ``functions`` (``array``): list of function names to fix; defaults to
    ``['get_class', 'php_sapi_name', 'phpversion', 'pi']``

* **function_typehint_space** [@Symfony]

  Add missing space between function's argument and its typehint.

* **general_phpdoc_annotation_remove**

  Configured annotations should be omitted from phpdocs.

  Configuration options:

  - ``annotations`` (``array``): list of annotations to remove, e.g. ``["author"]``;
    defaults to ``[]``

* **hash_to_slash_comment**

  Single line comments should use double slashes ``//`` and not hash ``#``.
  DEPRECATED: use ``single_line_comment_style`` instead.

* **header_comment**

  Add, replace or remove header comment.

  Configuration options:

  - ``commentType`` (``'comment'``, ``'PHPDoc'``): comment syntax type; defaults to
    ``'comment'``
  - ``header`` (``string``): proper header content; required
  - ``location`` (``'after_declare_strict'``, ``'after_open'``): the location of the
    inserted header; defaults to ``'after_declare_strict'``
  - ``separate`` (``'both'``, ``'bottom'``, ``'none'``, ``'top'``): whether the header should be
    separated from the file content with a new line; defaults to ``'both'``

* **heredoc_to_nowdoc**

  Convert ``heredoc`` to ``nowdoc`` where possible.

* **include** [@Symfony]

  Include/Require and file path should be divided with a single space.
  File path should not be placed under brackets.

* **increment_style** [@Symfony]

  Pre- or post-increment and decrement operators should be used if
  possible.

  Configuration options:

  - ``style`` (``'post'``, ``'pre'``): whether to use pre- or post-increment and
    decrement operators; defaults to ``'pre'``

* **indentation_type** [@PSR2, @Symfony]

  Code MUST use configured indentation type.

* **is_null** [@Symfony:risky]

  Replaces ``is_null($var)`` expression with ``null === $var``.

  *Risky rule: risky when the function ``is_null`` is overridden.*

  Configuration options:

  - ``use_yoda_style`` (``bool``): (deprecated) Whether Yoda style conditions should
    be used; defaults to ``true``

* **line_ending** [@PSR2, @Symfony]

  All PHP files must use same line ending.

* **linebreak_after_opening_tag**

  Ensure there is no code on the same line as the PHP open tag.

* **list_syntax**

  List (``array`` destructuring) assignment should be declared using the
  configured syntax. Requires PHP >= 7.1.

  Configuration options:

  - ``syntax`` (``'long'``, ``'short'``): whether to use the ``long`` or ``short`` ``list``
    syntax; defaults to ``'long'``

* **lowercase_cast** [@Symfony]

  Cast should be written in lower case.

* **lowercase_constants** [@PSR2, @Symfony]

  The PHP constants ``true``, ``false``, and ``null`` MUST be in lower case.

* **lowercase_keywords** [@PSR2, @Symfony]

  PHP keywords MUST be in lower case.

* **magic_constant_casing** [@Symfony]

  Magic constants should be referred to using the correct casing.

* **mb_str_functions**

  Replace non multibyte-safe functions with corresponding mb function.

  *Risky rule: risky when any of the functions are overridden.*

* **method_argument_space** [@PSR2, @Symfony]

  In method arguments and method call, there MUST NOT be a space before
  each comma and there MUST be one space after each comma. Argument lists
  MAY be split across multiple lines, where each subsequent line is
  indented once. When doing so, the first item in the list MUST be on the
  next line, and there MUST be only one argument per line.

  Configuration options:

  - ``ensure_fully_multiline`` (``bool``): ensure every argument of a multiline
    argument list is on its own line; defaults to ``false``
  - ``keep_multiple_spaces_after_comma`` (``bool``): whether keep multiple spaces
    after comma; defaults to ``false``

* **method_chaining_indentation**

  Method chaining MUST be properly indented. Method chaining with
  different levels of indentation is not supported.

* **method_separation**

  Methods must be separated with one blank line. DEPRECATED: use
  ``class_attributes_separation`` instead.

* **modernize_types_casting** [@Symfony:risky]

  Replaces ``intval``, ``floatval``, ``doubleval``, ``strval`` and ``boolval``
  function calls with according type casting operator.

  *Risky rule: risky if any of the functions ``intval``, ``floatval``, ``doubleval``, ``strval`` or ``boolval`` are overridden.*

* **multiline_comment_opening_closing**

  DocBlocks must start with two asterisks, multiline comments must start
  with a single asterisk, after the opening slash. Both must end with a
  single asterisk before the closing slash.

* **multiline_whitespace_before_semicolons**

  Forbid multi-line whitespace before the closing semicolon or move the
  semicolon to the new line for chained calls.

  Configuration options:

  - ``strategy`` (``'new_line_for_chained_calls'``, ``'no_multi_line'``): forbid
    multi-line whitespace or move the semicolon to the new line for chained
    calls; defaults to ``'no_multi_line'``

* **native_function_casing** [@Symfony]

  Function defined by PHP should be called using the correct casing.

* **native_function_invocation**

  Add leading ``\`` before function invocation of internal function to speed
  up resolving.

  *Risky rule: risky when any of the functions are overridden.*

  Configuration options:

  - ``exclude`` (``array``): list of functions to ignore; defaults to ``[]``

* **new_with_braces** [@Symfony]

  All instances created with new keyword must be followed by braces.

* **no_alias_functions** [@Symfony:risky]

  Master functions shall be used instead of aliases.

  *Risky rule: risky when any of the alias functions are overridden.*

* **no_blank_lines_after_class_opening** [@Symfony]

  There should be no empty lines after class opening brace.

* **no_blank_lines_after_phpdoc** [@Symfony]

  There should not be blank lines between docblock and the documented
  element.

* **no_blank_lines_before_namespace**

  There should be no blank lines before a namespace declaration.

* **no_break_comment** [@PSR2, @Symfony]

  There must be a comment when fall-through is intentional in a non-empty
  case body.

  Configuration options:

  - ``comment_text`` (``string``): the text to use in the added comment and to
    detect it; defaults to ``'no break'``

* **no_closing_tag** [@PSR2, @Symfony]

  The closing ``?>`` tag MUST be omitted from files containing only PHP.

* **no_empty_comment** [@Symfony]

  There should not be any empty comments.

* **no_empty_phpdoc** [@Symfony]

  There should not be empty PHPDoc blocks.

* **no_empty_statement** [@Symfony]

  Remove useless semicolon statements.

* **no_extra_blank_lines** [@Symfony]

  Removes extra blank lines and/or blank lines following configuration.

  Configuration options:

  - ``tokens`` (``array``): list of tokens to fix; defaults to ``['extra']``

* **no_extra_consecutive_blank_lines**

  Removes extra blank lines and/or blank lines following configuration.
  DEPRECATED: use ``no_extra_blank_lines`` instead.

  Configuration options:

  - ``tokens`` (``array``): list of tokens to fix; defaults to ``['extra']``

* **no_homoglyph_names** [@Symfony:risky]

  Replace accidental usage of homoglyphs (non ascii characters) in names.

  *Risky rule: renames classes and cannot rename the files. You might have string references to renamed code (``$$name``).*

* **no_leading_import_slash** [@Symfony]

  Remove leading slashes in use clauses.

* **no_leading_namespace_whitespace** [@Symfony]

  The namespace declaration line shouldn't contain leading whitespace.

* **no_mixed_echo_print** [@Symfony]

  Either language construct ``print`` or ``echo`` should be used.

  Configuration options:

  - ``use`` (``'echo'``, ``'print'``): the desired language construct; defaults to
    ``'echo'``

* **no_multiline_whitespace_around_double_arrow** [@Symfony]

  Operator ``=>`` should not be surrounded by multi-line whitespaces.

* **no_multiline_whitespace_before_semicolons**

  Multi-line whitespace before closing semicolon are prohibited.
  DEPRECATED: use ``multiline_whitespace_before_semicolons`` instead.

* **no_null_property_initialization**

  Properties MUST not be explicitly initialized with ``null``.

* **no_php4_constructor**

  Convert PHP4-style constructors to ``__construct``.

  *Risky rule: risky when old style constructor being fixed is overridden or overrides parent one.*

* **no_short_bool_cast** [@Symfony]

  Short cast ``bool`` using double exclamation mark should not be used.

* **no_short_echo_tag**

  Replace short-echo ``<?=`` with long format ``<?php echo`` syntax.

* **no_singleline_whitespace_before_semicolons** [@Symfony]

  Single-line whitespace before closing semicolon are prohibited.

* **no_spaces_after_function_name** [@PSR2, @Symfony]

  When making a method or function call, there MUST NOT be a space between
  the method or function name and the opening parenthesis.

* **no_spaces_around_offset** [@Symfony]

  There MUST NOT be spaces around offset braces.

  Configuration options:

  - ``positions`` (``array``): whether spacing should be fixed inside and/or outside
    the offset braces; defaults to ``['inside', 'outside']``

* **no_spaces_inside_parenthesis** [@PSR2, @Symfony]

  There MUST NOT be a space after the opening parenthesis. There MUST NOT
  be a space before the closing parenthesis.

* **no_superfluous_elseif**

  Replaces superfluous ``elseif`` with ``if``.

* **no_trailing_comma_in_list_call** [@Symfony]

  Remove trailing commas in list function calls.

* **no_trailing_comma_in_singleline_array** [@Symfony]

  PHP single-line arrays should not have trailing comma.

* **no_trailing_whitespace** [@PSR2, @Symfony]

  Remove trailing whitespace at the end of non-blank lines.

* **no_trailing_whitespace_in_comment** [@PSR2, @Symfony]

  There MUST be no trailing spaces inside comments and phpdocs.

* **no_unneeded_control_parentheses** [@Symfony]

  Removes unneeded parentheses around control statements.

  Configuration options:

  - ``statements`` (``array``): list of control statements to fix; defaults to
    ``['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case',
    'yield']``

* **no_unneeded_curly_braces** [@Symfony]

  Removes unneeded curly braces that are superfluous and aren't part of a
  control structure's body.

* **no_unneeded_final_method** [@Symfony]

  A final class must not have final methods.

* **no_unreachable_default_argument_value**

  In function arguments there must not be arguments with default values
  before non-default ones.

  *Risky rule: modifies the signature of functions; therefore risky when using systems (such as some Symfony components) that rely on those (for example through reflection).*

* **no_unused_imports** [@Symfony]

  Unused use statements must be removed.

* **no_useless_else**

  There should not be useless ``else`` cases.

* **no_useless_return**

  There should not be an empty return statement at the end of a function.

* **no_whitespace_before_comma_in_array** [@Symfony]

  In array declaration, there MUST NOT be a whitespace before each comma.

* **no_whitespace_in_blank_line** [@Symfony]

  Remove trailing whitespace at the end of blank lines.

* **non_printable_character** [@Symfony:risky, @PHP70Migration:risky, @PHP71Migration:risky]

  Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other
  invisible unicode symbols.

  *Risky rule: risky when strings contain intended invisible characters.*

  Configuration options:

  - ``use_escape_sequences_in_strings`` (``bool``): whether characters should be
    replaced with escape sequences in strings; defaults to ``false``

* **normalize_index_brace** [@Symfony]

  Array index should always be written by using square braces.

* **not_operator_with_space**

  Logical NOT operators (``!``) should have leading and trailing
  whitespaces.

* **not_operator_with_successor_space**

  Logical NOT operators (``!``) should have one trailing whitespace.

* **object_operator_without_whitespace** [@Symfony]

  There should not be space before or after object ``T_OBJECT_OPERATOR``
  ``->``.

* **ordered_class_elements**

  Orders the elements of classes/interfaces/traits.

  Configuration options:

  - ``order`` (``array``): list of strings defining order of elements; defaults to
    ``['use_trait', 'constant_public', 'constant_protected',
    'constant_private', 'property_public', 'property_protected',
    'property_private', 'construct', 'destruct', 'magic', 'phpunit',
    'method_public', 'method_protected', 'method_private']``

* **ordered_imports**

  Ordering use statements.

  Configuration options:

  - ``importsOrder`` (``array``, ``null``): defines the order of import types; defaults
    to ``null``
  - ``sortAlgorithm`` (``'alpha'``, ``'length'``): whether the statements should be
    sorted alphabetically or by length; defaults to ``'alpha'``

* **php_unit_construct** [@Symfony:risky]

  PHPUnit assertion method calls like "->assertSame(true, $foo)" should be
  written with dedicated method like "->assertTrue($foo)".

  *Risky rule: fixer could be risky if one is overriding PHPUnit's native methods.*

  Configuration options:

  - ``assertions`` (``array``): list of assertion methods to fix; defaults to
    ``['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame']``

* **php_unit_dedicate_assert** [@PHPUnit30Migration:risky, @PHPUnit32Migration:risky, @PHPUnit35Migration:risky, @PHPUnit43Migration:risky, @PHPUnit48Migration:risky, @PHPUnit50Migration:risky, @PHPUnit52Migration:risky, @PHPUnit54Migration:risky, @PHPUnit55Migration:risky, @PHPUnit56Migration:risky, @PHPUnit57Migration:risky, @PHPUnit60Migration:risky]

  PHPUnit assertions like "assertInternalType", "assertFileExists", should
  be used over "assertTrue".

  *Risky rule: fixer could be risky if one is overriding PHPUnit's native methods.*

  Configuration options:

  - ``functions`` (``null``): (deprecated, use ``target`` instead) List of assertions
    to fix (overrides ``target``); defaults to ``null``
  - ``target`` (``'3.0'``, ``'3.5'``, ``'5.0'``, ``'5.6'``, ``'newest'``): target version of
    PHPUnit; defaults to ``'5.0'``

* **php_unit_expectation** [@PHPUnit52Migration:risky, @PHPUnit54Migration:risky, @PHPUnit55Migration:risky, @PHPUnit56Migration:risky, @PHPUnit57Migration:risky, @PHPUnit60Migration:risky]

  Usages of ``->setExpectedException*`` methods MUST be replaced by
  ``->expectException*`` methods.

  *Risky rule: risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.*

  Configuration options:

  - ``target`` (``'5.2'``, ``'5.6'``, ``'newest'``): target version of PHPUnit; defaults to
    ``'newest'``

* **php_unit_fqcn_annotation** [@Symfony]

  PHPUnit annotations should be a FQCNs including a root namespace.

* **php_unit_mock** [@PHPUnit54Migration:risky, @PHPUnit55Migration:risky, @PHPUnit56Migration:risky, @PHPUnit57Migration:risky, @PHPUnit60Migration:risky]

  Usages of ``->getMock`` and
  ``->getMockWithoutInvokingTheOriginalConstructor`` methods MUST be
  replaced by ``->createMock`` or ``->createPartialMock`` methods.

  *Risky rule: risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.*

  Configuration options:

  - ``target`` (``'5.4'``, ``'5.5'``, ``'newest'``): target version of PHPUnit; defaults to
    ``'newest'``

* **php_unit_namespaced** [@PHPUnit48Migration:risky, @PHPUnit50Migration:risky, @PHPUnit52Migration:risky, @PHPUnit54Migration:risky, @PHPUnit55Migration:risky, @PHPUnit56Migration:risky, @PHPUnit57Migration:risky, @PHPUnit60Migration:risky]

  PHPUnit classes MUST be used in namespaced version, eg
  ``\PHPUnit\Framework\TestCase`` instead of ``\PHPUnit_Framework_TestCase``.

  *Risky rule: risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.*

  Configuration options:

  - ``target`` (``'4.8'``, ``'5.7'``, ``'6.0'``, ``'newest'``): target version of PHPUnit;
    defaults to ``'newest'``

* **php_unit_no_expectation_annotation** [@PHPUnit32Migration:risky, @PHPUnit35Migration:risky, @PHPUnit43Migration:risky, @PHPUnit48Migration:risky, @PHPUnit50Migration:risky, @PHPUnit52Migration:risky, @PHPUnit54Migration:risky, @PHPUnit55Migration:risky, @PHPUnit56Migration:risky, @PHPUnit57Migration:risky, @PHPUnit60Migration:risky]

  Usages of ``@expectedException*`` annotations MUST be replaced by
  ``->setExpectedException*`` methods.

  *Risky rule: risky when PHPUnit classes are overridden or not accessible, or when project has PHPUnit incompatibilities.*

  Configuration options:

  - ``target`` (``'3.2'``, ``'4.3'``, ``'newest'``): target version of PHPUnit; defaults to
    ``'newest'``
  - ``use_class_const`` (``bool``): use ::class notation; defaults to ``true``

* **php_unit_strict**

  PHPUnit methods like ``assertSame`` should be used instead of
  ``assertEquals``.

  *Risky rule: risky when any of the functions are overridden.*

  Configuration options:

  - ``assertions`` (``array``): list of assertion methods to fix; defaults to
    ``['assertAttributeEquals', 'assertAttributeNotEquals', 'assertEquals',
    'assertNotEquals']``

* **php_unit_test_annotation**

  Adds or removes @test annotations from tests, following configuration.

  *Risky rule: this fixer may change the name of your tests, and could cause incompatibility with abstract classes or interfaces.*

  Configuration options:

  - ``case`` (``'camel'``, ``'snake'``): whether to camel or snake case when adding the
    test prefix; defaults to ``'camel'``
  - ``style`` (``'annotation'``, ``'prefix'``): whether to use the @test annotation or
    not; defaults to ``'prefix'``

* **php_unit_test_class_requires_covers**

  Adds a default ``@coversNothing`` annotation to PHPUnit test classes that
  have no ``@covers*`` annotation.

* **phpdoc_add_missing_param_annotation**

  Phpdoc should contain @param for all params.

  Configuration options:

  - ``only_untyped`` (``bool``): whether to add missing ``@param`` annotations for
    untyped parameters only; defaults to ``true``

* **phpdoc_align** [@Symfony]

  All items of the given phpdoc tags must be aligned vertically.

  Configuration options:

  - ``tags`` (``array``): the tags that should be aligned; defaults to ``['param',
    'return', 'throws', 'type', 'var']``

* **phpdoc_annotation_without_dot** [@Symfony]

  Phpdocs annotation descriptions should not be a sentence.

* **phpdoc_indent** [@Symfony]

  Docblocks should have the same indentation as the documented subject.

* **phpdoc_inline_tag** [@Symfony]

  Fix phpdoc inline tags, make inheritdoc always inline.

* **phpdoc_no_access** [@Symfony]

  @access annotations should be omitted from phpdocs.

* **phpdoc_no_alias_tag** [@Symfony]

  No alias PHPDoc tags should be used.

  Configuration options:

  - ``replacements`` (``array``): mapping between replaced annotations with new
    ones; defaults to ``['property-read' => 'property', 'property-write' =>
    'property', 'type' => 'var', 'link' => 'see']``

* **phpdoc_no_empty_return** [@Symfony]

  @return void and @return null annotations should be omitted from
  phpdocs.

* **phpdoc_no_package** [@Symfony]

  @package and @subpackage annotations should be omitted from phpdocs.

* **phpdoc_no_useless_inheritdoc** [@Symfony]

  Classy that does not inherit must not have inheritdoc tags.

* **phpdoc_order**

  Annotations in phpdocs should be ordered so that param annotations come
  first, then throws annotations, then return annotations.

* **phpdoc_return_self_reference** [@Symfony]

  The type of ``@return`` annotations of methods returning a reference to
  itself must the configured one.

  Configuration options:

  - ``replacements`` (``array``): mapping between replaced return types with new
    ones; defaults to ``['this' => '$this', '@this' => '$this', '$self' =>
    'self', '@self' => 'self', '$static' => 'static', '@static' =>
    'static']``

* **phpdoc_scalar** [@Symfony]

  Scalar types should always be written in the same form. ``int`` not
  ``integer``, ``bool`` not ``boolean``, ``float`` not ``real`` or ``double``.

* **phpdoc_separation** [@Symfony]

  Annotations in phpdocs should be grouped together so that annotations of
  the same type immediately follow each other, and annotations of a
  different type are separated by a single blank line.

* **phpdoc_single_line_var_spacing** [@Symfony]

  Single line @var PHPDoc should have proper spacing.

* **phpdoc_summary** [@Symfony]

  Phpdocs summary should end in either a full stop, exclamation mark, or
  question mark.

* **phpdoc_to_comment** [@Symfony]

  Docblocks should only be used on structural elements.

* **phpdoc_trim** [@Symfony]

  Phpdocs should start and end with content, excluding the very first and
  last line of the docblocks.

* **phpdoc_types** [@Symfony]

  The correct case must be used for standard PHP types in phpdoc.

* **phpdoc_types_order**

  Sorts PHPDoc types.

  Configuration options:

  - ``null_adjustment`` (``'always_first'``, ``'always_last'``, ``'none'``): forces the
    position of ``null`` (overrides ``sort_algorithm``); defaults to
    ``'always_first'``
  - ``sort_algorithm`` (``'alpha'``, ``'none'``): the sorting algorithm to apply;
    defaults to ``'alpha'``

* **phpdoc_var_without_name** [@Symfony]

  @var and @type annotations should not contain the variable name.

* **pow_to_exponentiation** [@PHP56Migration:risky, @PHP70Migration:risky, @PHP71Migration:risky]

  Converts ``pow`` to the ``**`` operator.

  *Risky rule: risky when the function ``pow`` is overridden.*

* **pre_increment**

  Pre incrementation/decrementation should be used if possible.
  DEPRECATED: use ``increment_style`` instead.

* **protected_to_private** [@Symfony]

  Converts ``protected`` variables and methods to ``private`` where possible.

* **psr0**

  Classes must be in a path that matches their namespace, be at least one
  namespace deep and the class name should match the file name.

  *Risky rule: this fixer may change your class name, which will break the code that is depended on old name.*

  Configuration options:

  - ``dir`` (``string``): the directory where the project code is placed; defaults
    to ``''``

* **psr4** [@Symfony:risky]

  Class names should match the file name.

  *Risky rule: this fixer may change your class name, which will break the code that is depended on old name.*

* **random_api_migration** [@PHP70Migration:risky, @PHP71Migration:risky]

  Replaces ``rand``, ``srand``, ``getrandmax`` functions calls with their ``mt_*``
  analogs.

  *Risky rule: risky when the configured functions are overridden.*

  Configuration options:

  - ``replacements`` (``array``): mapping between replaced functions with the new
    ones; defaults to ``['getrandmax' => 'mt_getrandmax', 'rand' =>
    'mt_rand', 'srand' => 'mt_srand']``

* **return_type_declaration** [@Symfony]

  There should be one or no space before colon, and one space after it in
  return type declarations, according to configuration.

  Configuration options:

  - ``space_before`` (``'none'``, ``'one'``): spacing to apply before colon; defaults to
    ``'none'``

* **self_accessor** [@Symfony]

  Inside class or interface element "self" should be preferred to the
  class name itself.

* **semicolon_after_instruction** [@Symfony]

  Instructions must be terminated with a semicolon.

* **short_scalar_cast** [@Symfony]

  Cast ``(boolean)`` and ``(integer)`` should be written as ``(bool)`` and
  ``(int)``, ``(double)`` and ``(real)`` as ``(float)``.

* **silenced_deprecation_error** [@Symfony:risky]

  Ensures deprecation notices are silenced.

  *Risky rule: silencing of deprecation errors might cause changes to code behaviour.*

* **simplified_null_return**

  A return statement wishing to return ``void`` should not return ``null``.

* **single_blank_line_at_eof** [@PSR2, @Symfony]

  A PHP file without end tag must always end with a single empty line
  feed.

* **single_blank_line_before_namespace** [@Symfony]

  There should be exactly one blank line before a namespace declaration.

* **single_class_element_per_statement** [@PSR2, @Symfony]

  There MUST NOT be more than one property or constant declared per
  statement.

  Configuration options:

  - ``elements`` (``array``): list of strings which element should be modified;
    defaults to ``['const', 'property']``

* **single_import_per_statement** [@PSR2, @Symfony]

  There MUST be one use keyword per declaration.

* **single_line_after_imports** [@PSR2, @Symfony]

  Each namespace use MUST go on its own line and there MUST be one blank
  line after the use statements block.

* **single_line_comment_style** [@Symfony]

  Single-line comments and multi-line comments with only one line of
  actual content should use the ``//`` syntax.

  Configuration options:

  - ``comment_types`` (``array``): list of comment types to fix; defaults to
    ``['asterisk', 'hash']``

* **single_quote** [@Symfony]

  Convert double quotes to single quotes for simple strings.

* **space_after_semicolon** [@Symfony]

  Fix whitespace after a semicolon.

  Configuration options:

  - ``remove_in_empty_for_expressions`` (``bool``): whether spaces should be removed
    for empty ``for`` expressions; defaults to ``false``

* **standardize_not_equals** [@Symfony]

  Replace all ``<>`` with ``!=``.

* **static_lambda**

  Lambdas not (indirect) referencing ``$this`` must be declared ``static``.

  *Risky rule: risky when using "->bindTo" on lambdas without referencing to ``$this``.*

* **strict_comparison**

  Comparisons should be strict.

  *Risky rule: changing comparisons to strict might change code behavior.*

* **strict_param**

  Functions should be used with ``$strict`` param set to ``true``.

  *Risky rule: risky when the fixed function is overridden or if the code relies on non-strict usage.*

* **switch_case_semicolon_to_colon** [@PSR2, @Symfony]

  A case should be followed by a colon and not a semicolon.

* **switch_case_space** [@PSR2, @Symfony]

  Removes extra spaces between colon and case value.

* **ternary_operator_spaces** [@Symfony]

  Standardize spaces around ternary operator.

* **ternary_to_null_coalescing** [@PHP70Migration, @PHP71Migration]

  Use ``null`` coalescing operator ``??`` where possible. Requires PHP >= 7.0.

* **trailing_comma_in_multiline_array** [@Symfony]

  PHP multi-line arrays should have a trailing comma.

* **trim_array_spaces** [@Symfony]

  Arrays should be formatted like function/method arguments, without
  leading or trailing single line space.

* **unary_operator_spaces** [@Symfony]

  Unary operators should be placed adjacent to their operands.

* **visibility_required** [@PSR2, @Symfony, @PHP71Migration]

  Visibility MUST be declared on all properties and methods; abstract and
  final MUST be declared before the visibility; static MUST be declared
  after the visibility.

  Configuration options:

  - ``elements`` (``array``): the structural elements to fix (PHP >= 7.1 required
    for ``const``); defaults to ``['property', 'method']``

* **void_return** [@PHP71Migration:risky]

  Add void return type to functions with missing or empty return
  statements, but priority is given to ``@return`` annotations. Requires
  PHP >= 7.1.

  *Risky rule: modifies the signature of functions.*

* **whitespace_after_comma_in_array** [@Symfony]

  In array declaration, there MUST be a whitespace after each comma.

* **yoda_style** [@Symfony]

  Write conditions in Yoda style (``true``), non-Yoda style (``false``) or
  ignore those conditions (``null``) based on configuration.

  Configuration options:

  - ``equal`` (``bool``, ``null``): style for equal (``==``, ``!=``) statements; defaults to
    ``true``
  - ``identical`` (``bool``, ``null``): style for identical (``===``, ``!==``) statements;
    defaults to ``true``
  - ``less_and_greater`` (``bool``, ``null``): style for less and greater than (``<``,
    ``<=``, ``>``, ``>=``) statements; defaults to ``null``


The ``--dry-run`` option displays the files that need to be
fixed but without actually modifying them:

.. code-block:: bash

    $ php php-cs-fixer.phar fix /path/to/code --dry-run

Config file
-----------

Instead of using command line options to customize the rule, you can save the
project configuration in a ``.php_cs.dist`` file in the root directory of your project.
The file must return an instance of `PhpCsFixer\\ConfigInterface <https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v2.10.0/src/ConfigInterface.php>`_
which lets you configure the rules, the files and directories that
need to be analyzed. You may also create ``.php_cs`` file, which is
the local configuration that will be used instead of the project configuration. It
is a good practice to add that file into your ``.gitignore`` file.
With the ``--config`` option you can specify the path to the
``.php_cs`` file.

The example below will add two rules to the default list of PSR2 set rules:

.. code-block:: php

    <?php

    $finder = PhpCsFixer\Finder::create()
        ->exclude('somedir')
        ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
        ->in(__DIR__)
    ;

    return PhpCsFixer\Config::create()
        ->setRules([
            '@PSR2' => true,
            'strict_param' => true,
            'array_syntax' => ['syntax' => 'short'],
        ])
        ->setFinder($finder)
    ;

**NOTE**: ``exclude`` will work only for directories, so if you need to exclude file, try ``notPath``.

See `Symfony\\Finder <http://symfony.com/doc/current/components/finder.html>`_
online documentation for other `Finder` methods.

You may also use a blacklist for the rules instead of the above shown whitelist approach.
The following example shows how to use all ``Symfony`` rules but the ``full_opening_tag`` rule.

.. code-block:: php

    <?php

    $finder = PhpCsFixer\Finder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return PhpCsFixer\Config::create()
        ->setRules([
            '@Symfony' => true,
            'full_opening_tag' => false,
        ])
        ->setFinder($finder)
    ;

You may want to use non-linux whitespaces in your project. Then you need to
configure them in your config file.

.. code-block:: php

    <?php

    return PhpCsFixer\Config::create()
        ->setIndent("\t")
        ->setLineEnding("\r\n")
    ;

By using ``--using-cache`` option with ``yes`` or ``no`` you can set if the caching
mechanism should be used.

Caching
-------

The caching mechanism is enabled by default. This will speed up further runs by
fixing only files that were modified since the last run. The tool will fix all
files if the tool version has changed or the list of rules has changed.
Cache is supported only for tool downloaded as phar file or installed via
composer.

Cache can be disabled via ``--using-cache`` option or config file:

.. code-block:: php

    <?php

    return PhpCsFixer\Config::create()
        ->setUsingCache(false)
    ;

Cache file can be specified via ``--cache-file`` option or config file:

.. code-block:: php

    <?php

    return PhpCsFixer\Config::create()
        ->setCacheFile(__DIR__.'/.php_cs.cache')
    ;

Using PHP CS Fixer on CI
------------------------

Require ``friendsofphp/php-cs-fixer`` as a ``dev`` dependency:

.. code-block:: bash

    $ ./composer.phar require --dev friendsofphp/php-cs-fixer

Then, add the following command to your CI:

.. code-block:: bash

    $ CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB "${COMMIT_RANGE}")
    $ if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php_cs(\\.dist)?|composer\\.lock)$"; then IFS=$'\n' EXTRA_ARGS=('--path-mode=intersection' '--' ${CHANGED_FILES[@]}); fi
    $ vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run --stop-on-violation --using-cache=no "${EXTRA_ARGS[@]}"

Where ``$COMMIT_RANGE`` is your range of commits, eg ``$TRAVIS_COMMIT_RANGE`` or ``HEAD~..HEAD``.

Exit codes
----------

Exit code is built using following bit flags:

*  0 OK.
*  1 General error (or PHP minimal requirement not matched).
*  4 Some files have invalid syntax (only in dry-run mode).
*  8 Some files need fixing (only in dry-run mode).
* 16 Configuration error of the application.
* 32 Configuration error of a Fixer.
* 64 Exception raised within the application.

(applies to exit codes of the `fix` command only)

Helpers
-------

Dedicated plugins exist for:

* `Atom`_
* `NetBeans`_
* `PhpStorm`_
* `Sublime Text`_
* `Vim`_

Contribute
----------

The tool comes with quite a few built-in fixers, but everyone is more than
welcome to `contribute`_ more of them.

Fixers
~~~~~~

A *fixer* is a class that tries to fix one CS issue (a ``Fixer`` class must
implement ``FixerInterface``).

Configs
~~~~~~~

A *config* knows about the CS rules and the files and directories that must be
scanned by the tool when run in the directory of your project. It is useful for
projects that follow a well-known directory structures (like for Symfony
projects for instance).

.. _php-cs-fixer.phar: http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar
.. _Atom:              https://github.com/Glavin001/atom-beautify
.. _NetBeans:          http://plugins.netbeans.org/plugin/49042/php-cs-fixer
.. _PhpStorm:          https://medium.com/@valeryan/how-to-configure-phpstorm-to-use-php-cs-fixer-1844991e521f
.. _Sublime Text:      https://github.com/benmatselby/sublime-phpcs
.. _Vim:               https://github.com/stephpy/vim-php-cs-fixer
.. _contribute:        https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/CONTRIBUTING.md
