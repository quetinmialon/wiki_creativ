document.addEventListener('DOMContentLoaded', function() {
    // Gestion des rôles Admin
    document.querySelectorAll('.role-checkbox').forEach(roleCheckbox => {
        let adminSwitch = document.getElementById('admin_switch_' + roleCheckbox.dataset.role);
        if (!adminSwitch) return;

        let adminRoleId = adminSwitch.dataset.adminRole;

        // Activation du rôle Admin quand le switch est activé
        adminSwitch.addEventListener('change', function() {
            if (this.checked) {
                let adminCheckbox = document.createElement('input');
                adminCheckbox.type = 'hidden';
                adminCheckbox.name = 'role_ids[]';
                adminCheckbox.value = adminRoleId;
                adminCheckbox.id = 'hidden_admin_role_' + adminRoleId;
                document.querySelector('form').appendChild(adminCheckbox);
            } else {
                let hiddenAdminRole = document.getElementById('hidden_admin_role_' + adminRoleId);
                if (hiddenAdminRole) hiddenAdminRole.remove();
            }
        });

        // Désactivation du switch Admin si le rôle principal est décoché
        roleCheckbox.addEventListener('change', function() {
            if (!this.checked) {
                adminSwitch.checked = false;
                let hiddenAdminRole = document.getElementById('hidden_admin_role_' + adminRoleId);
                if (hiddenAdminRole) hiddenAdminRole.remove();
            }
        });
    });
});
