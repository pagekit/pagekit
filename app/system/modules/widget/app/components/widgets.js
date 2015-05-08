/**
 * Widgets manager.
 */

module.exports = {

    sections: [],
    components: {},
    
    addSection: function(options) {
        this.components[options.name] = options;
        this.sections.push(options);
    }

};
