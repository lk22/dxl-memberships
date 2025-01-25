(function($) {

    const frontendMember = {
        init: function() {
            this.container = $('.dxl-membership-frontend');
            this.bindActions();
        },

        bindActions: function() {
            const self = this;
            const memberForm = self.container.find('.frontendCreateMemberForm');

            memberForm.submit((e) => {
                e.preventDefault();

                $.ajax({
                    method: 'POST', 
                    url: dxl_member_vars.ajaxurl, 
                    data: {
                        action: dxl_member_vars.action[0],
                        dxl_member_nonce: dxl_member_vars.dxl_member_nonce,
                        member: {
                            email: memberForm.find('#member-email').val(),
                            gender: memberForm.find('#member-gender').val(),
                        }
                    },
                    success: (response) => { 
                        const json = JSON.parse(response);
                        const hasError = self.checkForResponseError(json.member);

                        if( !hasError ){
                            $('#membershipCreatedModal').modal('show');
                        } else {
                            $('#membershipCreatedModal').modal('show');
                        }
                    },
                    error: (error) => {
                        $('.error-message').html("<h4>Der gik noget galt ved oprettelse af din ansøgning</h4>");
                    }
                });
            });
        },
        /**
        * 
        * @param object response object 
        * @param string module 
        * @returns 
        */
        checkForResponseError: function(response) {
            if( response.error ) {
                return true;
            }

            return false;
        }
    };

    frontendMember.init();
})(jQuery);