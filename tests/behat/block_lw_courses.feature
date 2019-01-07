@block @block_lw_courses
Feature: View the My Courses block on the dashboard and test it's functionality
  In order to view the My Courses block on the dashboard
  As an admin
  I can configure the My Courses block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | S1 |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "categories" exist:
      | name        | category | idnumber |
      | Category 1  | 0        | CAT1     |
      | Category 2  | CAT1     | CAT2     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | CAT1     |
      | Course 3 | C3        | CAT2     |
    And the following config values are set as admin:
      | messaging | 1 |
      | messagingallusers | 1 |
    And I log in as "admin"
    And I wait "5" seconds

  Scenario: View the block by a user without any enrolments
    Given I log out
    And I log in as "student1"
    And I am on homepage
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "No course information to show" in the "My Courses" "block"

  Scenario: View the block by a user with several enrolments
    Given the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Course 1" in the "My Courses" "block"
    And I should see "Course 2" in the "My Courses" "block"

  Scenario: View the block by a user with several enrolments and limit the number of courses.
    Given the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    And I select "1" from the "Number of courses to display:" singleselect
    Then I should see "Course 1" in the "My Courses" "block"
    And I should see "You have 2 hidden courses"
    And I should not see "Course 2" in the "My Courses" "block"
    And I should not see "Course 3" in the "My Courses" "block"
    And I follow "Show all courses"
    And I should see "Course 1" in the "My Courses" "block"
    And I should see "Course 2" in the "My Courses" "block"
    And I should see "Course 3" in the "My Courses" "block"

  Scenario: View the block by a user with several enrolments and an admin set default max courses.
    Given the following config values are set as admin:
      | defaultmaxcourses | 2 | block_lw_courses |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Course 1" in the "My Courses" "block"
    And I should see "Course 2" in the "My Courses" "block"
    And I should see "You have 1 hidden course"
    And I select "Always show all" from the "Number of courses to display:" singleselect
    And I should see "Course 3" in the "My Courses" "block"
    And I should not see "You have 1 hidden course"

  Scenario: View the block by a user with several enrolments and an admin enforced maximum displayed courses.
    Given the following config values are set as admin:
      | defaultmaxcourses      | 2 | block_lw_courses |
      | forcedefaultmaxcourses | 1 | block_lw_courses |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Course 1" in the "My Courses" "block"
    And I should see "Course 2" in the "My Courses" "block"
    And I should see "You have 1 hidden course"
    And I should not see "Always show all" in the "My Courses" "block"

  Scenario: View the block by a user with the welcome area enabled and messaging disabled.
    Given the following config values are set as admin:
      | showwelcomearea | 1 | block_lw_courses |
      | messaging       | 0 |                       |
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Welcome Student" in the "My Courses (lw_courses)" "block"
    And I should not see "messages" in the "My Courses (lw_courses)" "block"

  Scenario: View the block by a user with both the welcome area and messaging enabled.
    Given the following config values are set as admin:
      | showwelcomearea | 1 | block_lw_courses |
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Welcome Student" in the "My Courses (lw_courses)" "block"
    And I should see "You have no unread messages" in the "My Courses (lw_courses)" "block"
    And I follow "messages"
    And I should see "No messages"

  @javascript
  Scenario: View the block by a user with the welcome area and the user having messages.
    Given the following config values are set as admin:
      | showwelcomearea | 1 | block_lw_courses |
    And I log out
    And I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    And I should see "Welcome Student" in the "My Courses (lw_courses)" "block"
    And I should see "You have no unread messages" in the "My Courses (lw_courses)" "block"
    And I follow "messages"
    And I send "This is message 1" message to "Teacher 1" user
    And I send "This is message 2" message to "Teacher 1" user
    When I log out
    And I log in as "teacher1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Welcome Teacher" in the "My Courses (lw_courses)" "block"
    And I should see "You have 2 unread messages" in the "My Courses (lw_courses)" "block"

  Scenario: View the block by a user with the parent categories displayed.
    Given the following config values are set as admin:
      | showcategories | Parent category only | block_lw_courses |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Miscellaneous" in the "My Courses" "block"
    And I should see "Category 1" in the "My Courses" "block"
    And I should see "Category 2" in the "My Courses" "block"
    And I should not see "Category 1 / Category 1" in the "My Courses" "block"

  Scenario: View the block by a user with the full categories displayed.
    Given the following config values are set as admin:
      | showcategories | 2 | block_lw_courses |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
    And I log out
    When I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Miscellaneous" in the "My Courses" "block"
    And I should see "Category 1 / Category 2" in the "My Courses" "block"

  @javascript
  Scenario: View the block by a user with the show children option enabled.
    Given the following config values are set as admin:
      | showchildren | 1 | block_lw_courses |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And I log out
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Manage enrol plugins" in site administration
    And I click on "Enable" "link" in the "Course meta link" "table_row"
    And I am on site homepage
    And I follow "Course 2"
    And I add "Course meta link" enrolment method with:
      | Link course | C1 |
    And I log out
    And I log out
    And I log in as "student1"
    And I press "Customise this page"
    And I add the "My Courses (lw_courses)" block
    Then I should see "Course 1" in the "My Courses" "block"
    And I should see "Course 2" in the "My Courses" "block"
    And I should see "Includes C1" in the "My Courses" "block"