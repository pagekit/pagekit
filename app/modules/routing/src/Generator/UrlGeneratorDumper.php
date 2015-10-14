<?php

namespace Pagekit\Routing\Generator;

use Symfony\Component\Routing\Generator\Dumper\GeneratorDumper;

/**
 * UrlGeneratorDumper creates a PHP class able to generate URLs for a given set of routes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 * @copyright (c) Fabien Potencier <fabien@symfony.com>
 */
class UrlGeneratorDumper extends GeneratorDumper
{
    /**
     * Dumps a set of routes to a PHP class.
     *
     * @param  array  $options
     * @return string
     */
    public function dump(array $options = [])
    {
        $options = array_merge([
            'class'      => 'ProjectUrlGenerator',
            'base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
        ], $options);

        return <<<EOF
<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * {$options['class']}
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class {$options['class']} extends {$options['base_class']}
{
    private static \$declaredRoutes = {$this->generateDeclaredRoutes()};

    /**
     * Constructor.
     */
    public function __construct(RequestContext \$context, LoggerInterface \$logger = null)
    {
        \$this->context = \$context;
        \$this->logger = \$logger;
    }

{$this->generateGenerateMethod()}
}

EOF;
    }

    /**
     * Generates PHP code representing an array of defined routes
     * together with the routes properties (e.g. requirements).
     *
     * @return string
     */
    private function generateDeclaredRoutes()
    {
        $routes = "array(\n";
        foreach ($this->getRoutes()->all() as $name => $route) {
            $compiledRoute = $route->compile();

            $properties = [];
            $properties[] = $compiledRoute->getVariables();
            $properties[] = $route->getDefaults();
            $properties[] = $route->getRequirements();
            $properties[] = $compiledRoute->getTokens();
            $properties[] = $compiledRoute->getHostTokens();
            $properties[] = $route->getSchemes();

            $routes .= sprintf("        '%s' => %s,\n", $name, str_replace("\n", '', var_export($properties, true)));
        }
        $routes .= '    )';

        return $routes;
    }

    /**
     * Generates PHP code representing the `generate` method that implements the UrlGeneratorInterface.
     *
     * @return string
     */
    private function generateGenerateMethod()
    {
        return <<<EOF
    public function generate(\$name, \$parameters = [], \$referenceType = self::ABSOLUTE_PATH)
    {
        if (!isset(self::\$declaredRoutes[\$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', \$name));
        }

        list(\$variables, \$defaults, \$requirements, \$tokens, \$hostTokens, \$requiredSchemes) = self::\$declaredRoutes[\$name];

        return \$this->doGenerate(\$variables, \$defaults, \$requirements, \$tokens, \$parameters, \$name, \$referenceType, \$hostTokens, \$requiredSchemes);
    }

    public function getRouteProperties(\$name)
    {
        if (isset(self::\$declaredRoutes[\$name])) {
            return self::\$declaredRoutes[\$name];
        }
    }
EOF;
    }
}
