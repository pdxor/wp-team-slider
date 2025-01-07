# wp-team-slider
A basic shortcode based team slider 
Creates a team member post type and displays them in a sliding carousel

Create a new directory called team-slider in your WordPress plugins directory (wp-content/plugins/)
Save the code above as team-slider.php in that directory
Activate the plugin through the WordPress admin panel

The plugin will:

Create a new "Team Members" post type in your WordPress admin
Add featured image support if your theme doesn't have it
Create a CSS file in a css subdirectory when activated
Provide the [team_slider] shortcode

To add team members:

Go to "Team Members" in your WordPress admin
Click "Add New"
Enter the team member's name as the title
Set their photo as the featured image
Publish the team member

Then use the [team_slider] shortcode anywhere you want to display the slider.
The plugin includes:

Clean activation/deactivation hooks
Proper file organization
Security best practices
Translation support
Default avatar fallback
Organized CSS in a separate file
Clean uninstall functionality
