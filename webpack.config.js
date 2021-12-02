var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath( Encore.isProduction() ? 'public/assets/' : 'public/build/' )
    .setPublicPath(Encore.isProduction() ? '/assets' : '/build')
    .enableSingleRuntimeChunk()
    .autoProvidejQuery()
    .addEntry('app', './assets/js/app.js')
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(!Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .autoProvidejQuery()
;
if (!Encore.isProduction()) {
    Encore
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
}

module.exports = Encore.getWebpackConfig();
