Module consists of two parts:
    - Projects folder
    - Part of code inside file /app/design/frontend/TemplateMonster/theme007_child/Magento_Catalog/templates/product/view/addtocart.phtml

Module structure:
    - Api
        - ProjectsInterface                 - this file consist all methods which you can use in api routes (etc/webapi)

    - Block
        - Index                             - Here you find methods which used in .phtml files (ex getUserProjects() or etc)
        - SingleProject                     - analog of Index but for using in project page, where you see all rooms and products

    - Controller
        - Adminhtml                         - Controller for admin pages
            - Projects
                - Index                     - just simple controller for creating table with existing projects
                - ...
        - Customer
            - Add                           - When we add all project to cart (adding products to cart)
            - Index                         - When we trying to create new room or project or just want to see a list of projects (bad)
            - Projects                      - if this page got Post data - it creates Project(adn room, if we have room data)
                                              else it show you a project with id from GET params
            - Room                          -

    - etc
        - adminhtml
            - menu.xml                      - configure for displaying our item in menu inside admin page
            - routes.xml                    - config for admin part of module
        - frontend
            - routes.xml                    - config for all module (site.local/ourModule/controllerFolder/Controller.php)
        - di.xml                            - Configuration of this module (API, controllers, etc)
        - module.xml                        - setup version, module declaration
        - webapi.xml                        - routes for API requests (example of api request 'http://ambassador.local/rest/V1/projects/add-project-to-cart/28')

    - Model                                 - here placed all models of this module

    - Setup
        - InstallSchema.php                 - Create db tables when install module
        - UpgradeSchema.php                 - Add culumns to existing db when upgrade it

    - Ui
        - Component
            - Listing
                - Columns
                    - ProjectActions.php    - We add column in admin table with link to project

    - view
        - adminhtml
            - layout
                - projects_projects_index.xml - Grid in admin panel
            - templates
                - projects.phtml            - empty
            - ui_component
                - mediapark_projects_project_listing.xml - creating grid block for admin panel
        - frontend
            - layout
            - templates
