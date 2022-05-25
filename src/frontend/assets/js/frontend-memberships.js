console.log(dxl_member_vars);
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


                const data = {
                    action: dxl_member_vars.action[0],
                    dxl_member_nonce: dxl_member_vars.dxl_member_nonce,
                    member: {
                        name: memberForm.find('#member-name').val(),
                        gamertag: memberForm.find('#member-gamertag').val(),
                        email: memberForm.find('#member-email').val(),
                        phone: memberForm.find('#member-phone').val(),
                        birthyear: memberForm.find('#member-birthdate').val(),
                        gender: memberForm.find('#member-gender').val(),
                        memberNumber: memberForm.find('#member-number').val(),
                        address: memberForm.find('#member-address').val(),
                        zipcode: memberForm.find('#member-zipcode').val(),
                        city: memberForm.find('#member-town').val(),
                        municipality: memberForm.find('#member-municipality').val(),
                        membership: memberForm.find('#member-membership').val()
                    }
                }

                console.log(data);

                $.ajax({
                    method: 'POST', 
                    url: dxl_member_vars.ajaxurl, 
                    data: {
                        action: dxl_member_vars.action[0],
                        dxl_member_nonce: dxl_member_vars.dxl_member_nonce,
                        member: {
                            name: memberForm.find('#member-name').val(),
                            gamertag: memberForm.find('#member-gamertag').val(),
                            email: memberForm.find('#member-email').val(),
                            phone: memberForm.find('#member-phone').val(),
                            birthyear: memberForm.find('#member-birthdate').val(),
                            gender: memberForm.find('#member-gender').val(),
                            memberNumber: memberForm.find('#member-number').val(),
                            address: memberForm.find('#member-address').val(),
                            zipcode: memberForm.find('#member-zipcode').val(),
                            city: memberForm.find('#member-town').val(),
                            municipality: memberForm.find('#member-municipality').val(),
                            membership: memberForm.find('#member-membership').val()
                        }
                    },
                    success: (response) => { 
                        console.log(response) 
                        const json = JSON.parse(response);

                        const hasError = self.checkForResponseError(json.member);

                        if( !hasError ){
                            $('.success-container').find('.headline').html("<h2>Du er oprettet</h2>");
                            $('.success-container').find('.message').html('<p>' + json.member.response + '</p>');
                            memberForm.hide();
                            $('.success-container').show();
                            
                        } else {
                            $('.error-message').html("<h4>" + json.member.response + "</h4>");
                        }
                    },
                    error: (error) => {console.log(error);}
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