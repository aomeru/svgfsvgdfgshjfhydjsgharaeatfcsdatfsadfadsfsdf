ACL
    implement ACL
        Roles
            fix adding roles, minify or switch to Vue
        Permission
        Allocation

AUTH
    Check login
    clear session and redirect if not logged in

USERS
    Implement Role Management
    Read only edits
    // implement modal views for roles

LEAVE
    APPLICATION
        Apply for leave
            No if already on leave
            use existing record if not applied yet
            if application exists and submitted, allow process first or send cancellation information
        Cancel Leave
            Cancel button only if not approved by HR
        HR Action
            Approval
            Decline
            Deference
            Status
                Mark leave request status and leave status
                Add Leave log for item
        Call off leave action
            Only HR after approval, through request
            Check if leave has been exhausted
    CHECKS
        On leave check

Check logging
