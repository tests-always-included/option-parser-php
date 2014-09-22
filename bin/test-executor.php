<?php


function pad($number) {
    return str_repeat(' ', $number);
}

function displayArray($what, $spacesIndent) {
    foreach ($what as $item) {
        display(pad($spacesIndent) . '-', $item, $spacesIndent);
    }
}

function displayObject($what, $spacesIndent) {
    foreach ($what as $key => $value) {
        display(pad($spacesIndent) . $key . ':', $value, $spacesIndent);
    }
}

function display($label, $value, $previousIndent) {
    $nextIndent = $previousIndent + 4;

    if (is_bool($value)) {
        echo $label . ' ';
        echo $value ? 'true' : 'false';
        echo "\n";
    } else if (is_string($value) || is_numeric($value)) {
        echo $label . ' ' . $value . "\n";
    } else if (is_array($value) && isset($value[0])) {
        echo $label . "\n";
        displayArray($value, $nextIndent);
    } else if (is_object($value) || is_array($value)) {
        echo $label . "\n";
        displayObject($value, $nextIndent);
    } else {
        echo 'Unknown thing: ' . $value . "\n";
    }
}

require_once(__DIR__ . '/../src/OptionParser.class.php');
$parser = new OptionParser();
$parser->programName('test-executor');

$parser->addOption('b', 'boolean', 'Boolean flag')
    ->action(function () {
        echo "Boolean\n";
    });

$parser->addOption(Array('h', '?'), 'help', 'This help message')
    ->action($parser->helpAction());

$parser->addOption('z', 'hidden')
    ->action(function () {
        echo "Hidden option triggered\n";
    });

$parser->addOption(null, 'lowercase', 'Only allows lowercase values')
    ->argument('STRING')
    ->validation(function ($value) {
        if (!preg_match('/^[a-z]+$/', $value)) {
            return 'Only lowercase allowed';
        }
    });

$parser->addOption(Array('m', 'M', '9'), Array('many-ways', 'multitude'), 'Option can be used many ways');

$parser->addOption('o', 'optional', 'Optional argument')
    ->argument('VALUE', false)
    ->action(function ($value) {
        if ($value !== null) {
            echo 'Optional: ' . $value . "\n";
        } else {
            echo "Optional parameter, no value\n";
        }
    });

$parser->addOption('r', 'required', 'Required argument')
    ->argument('DATA')
    ->action(function ($value) {
        echo 'Required: ' . $value . "\n";
    });

$parser->addOption('s', null, 'This option should just barely wrap-around-to-the-next-line-but-it-should-chop-this-super-long-word up.');

$parser->addOption(Array('w', 'W'), 'wrapping-of-long-description', 'This is a very long description of an option.  It ensures that the text will wrap around and around.  By forcing it to be extremely long we can confirm that implementations perform the proper wrapping and line breaks in the right locations.');

try {
    $unparsed = $parser->parse();
    $output = new StdClass();
    $output->getopt = $parser->getopt();
    $output->unparsed = $unparsed;
    displayObject($output, 0);
} catch (Exception $ex) {
    echo $ex->toString();
}

