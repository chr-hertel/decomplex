import Encore from '@symfony/webpack-encore';

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')

    .disableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()

    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .enablePostCssLoader()
    .autoProvidejQuery()
    .autoProvideVariables({
        CodeMirror: 'codemirror',
    })

    // Encore 7 no longer bundles a CSS minifier, it has to be picked explicitly
    .configureCssMinimizerPlugin((options, MinimizerPlugin) => {
        options.minify = MinimizerPlugin.cssnanoMinify;
    })

    // configure Babel
    .configureBabel((config) => {
        config.plugins.push([
            'polyfill-corejs3',
            { method: 'usage-global', version: '3.49' },
        ]);
    })
;

export default await Encore.getWebpackConfig();
