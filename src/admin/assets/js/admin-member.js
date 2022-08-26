
(function($, d) {
    $(window).ready(function() {
    
        const DXLAdminMember = {

            /**
             * Initialize member module actions
             */
            init: function() {
                this.container = $('.dxl-admin-members');
                this.modal = $('.modal');
                this.validated = {
                    failed: false,
                    fields: []
                };
                this.core = DXLCore;
                this.initializeActions();
            },

            /**
             * initialize all actions bound to the module
             */
            initializeActions: function() {
                const self = this;

                $('.modal').find('.close-modal').click(() => self.core.closeModal());

                self.container.find('.close-modal').click(() => {
                    self.core.closeModal()
                });

                self.container.find('.create-new-membership-btn').click((e) => {
                    self.core.openModal('.createMembershipsModal');
                })

                self.container.find('.create-new-member-btn').click((e) => {
                    e.preventDefault();
                    self.core.openModal('.createMemberModal');
                })

                self.container.find('.open-edit-member-modal-btn').click((e) => {
                    e.preventDefault();
                    self.core.openModal('.editMemberModal');
                })

                self.container.find('.close-modal').click((e) => {
                    e.preventDefault();
                    self.core.closeModal();
                });

                self.container.find('.search-members-btn').click(() => {
                    self.container.find('.searchbar').toggleClass('closed');
                });

                // when changing the search field to search from (default is name field)
                const searchbar = self.container.find('.searchbar');
                
                searchbar.find('#search-field-selection').change((e) => {
                    let value = e.target.value

                    searchbar.find('form').find('.search-field').html("<input type='text' name='member-" + value + "-field' placeholder='Search " + value + " here' id='member-" + value + "-field' required/>");
                    let searching = false;
                    let searchTimer;
                    searchbar.find('.search-member-button').click((e) => {
                        console.log(value);
                        e.preventDefault();
                        const searchValue = searchbar.find('#member-' + value + '-field').val();
                        self.core.request.data.member = {
                            field: {
                                name: value,
                                fieldValue: searchValue
                            }
                        };

                        self.core.sendRequest('dxl_member_search', 'GET', self.core.request.url, self.core.request.data, (response) => {
                            console.log(response);

                            const members = JSON.parse(response).member;
                            const hasError = self.core.checkForResponseError(members);

                            if( hasError ) {
                                $.toast({
                                    title: "Fejl",
                                    text: member.response,
                                    icon: "error",
                                    position: "bottom-right"
                                });
                                return false;
                            }

                            if( !members.data.length > 0 ) {
                                searchbar.find('.search-member-list').html("<div class='alert alert-danger alert-no-searh-results'>Fandt ingen medlemmer</div>");
                                return false;
                            } 
                               
                            $.each(members.data, (index, member) => {
                                searchbar.find('.search-member-list').append("<div class='result'><h3><a href='admin.php?page=dxl-members&action=details&id="+member.id+"'><span>" + member.name + "</span> - <span>" + member.gamertag + "</span></a></h3></div>");
                            });

                        }, (error) => {
                            console.log(error);
                        }, () => {
                            searchbar.find('.search-member-list').html('');
                            $.toast({
                                title: "Søger",
                                text: "Henter medlemmer",
                                icon: "info",
                                position: "bottom-right"
                            });
                        })
                    })
                });

                /**
                 * Creating a new member resource
                 */
                self.container.find('.submit-create-member').click((e) => {
                    e.preventDefault();
                    self.validated.failed = false;
                    self.validated.fields = [];
                    const form = self.container.find('.adminCreateMemberForm');
                    self.core.request.data.action = "dxl_admin_create_member";
                    self.core.request.data.member = {};
                    self.core.request.data.member.name = form.find('#member-name').val();
                    self.core.request.data.member.gamertag = form.find('#member-gamertag').val();
                    self.core.request.data.member.email = form.find('#member-email').val();
                    self.core.request.data.member.phone = form.find('#member-phone').val();
                    self.core.request.data.member.birthyear = form.find('#member-birthdate').val();
                    self.core.request.data.member.gender = form.find('#member-gender').val();
                    self.core.request.data.member.member_number = form.find('#member-number').val();
                    self.core.request.data.member.address = form.find('#member-address').val();
                    self.core.request.data.member.zipcode = form.find('#member-zipcode').val();
                    self.core.request.data.member.city = form.find('#member-town').val();
                    self.core.request.data.member.municipality = form.find('#member-municipality').val();
                    self.core.request.data.member.membership = form.find('#member-membership').val();
                    self.core.request.data.member.auto_renew = form.find('#member-renew:checked').val();
                    
                    if( self.core.request.data.member.name.length < 1 ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Navn');
                    }

                    if( self.core.request.data.member.gamertag.length < 1 ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Gamertag');
                    }

                    if( 
                        self.core.request.data.member.email.length < 1 
                    ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Email')
                    }

                    if( self.core.request.data.member.phone.length < 1 || self.core.request.data.member.phone.length > 8 ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Telefon nummer');
                    }

                    if( !self.core.request.data.member.birthyear.length > 0 ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Fødselsår');
                    }

                    if( !self.core.request.data.member.gender ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Køn');
                    }

                    if( self.core.request.data.member.address.length < 1 ){
                        self.validated.failed = true;
                        self.validated.fields.push('Adresse');
                    }

                    // if zipcode is not precisly 4 characters
                    if( self.core.request.data.member.zipcode.length !== 4 ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Postnummer');
                    }

                    if( self.core.request.data.member.city.length < 1 ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Bynavn');
                    }

                    if( self.core.request.data.member.municipality.length < 1 ) {
                        self.validated.failed = true;
                        self.validated.fields.push('Komunne');
                    }

                    if( this.validated.failed ) {
                        let validatedMessage = "Der opstod en fejl i valideringen af medlems oprettelsen i følgende felter: ";
                        self.validated.fields.map((field) => {
                            validatedMessage += "" + field + ", ";
                        });
                        $('.validated-message').addClass('error').html('<p>' + validatedMessage + '</p>').show();
                    }

                    if( self.validated.failed == false ) {
                        self.core.sendRequest('POST', self.core.request.url, self.core.request.data, (response) => {
                            console.log(response);

                            const json = JSON.parse(response);
                            const hasError = self.core.checkForResponseError(json.member);

                            if( hasError ) {
                                $.toast({
                                    title: "fejl",
                                    text: json.member.response,
                                    icon: "error",
                                    position: "bottom-right"
                                });

                                return false;
                            }

                            $.toast({
                                text:json.member.response,
                                icon: 'success',
                                position: 'bottom-right'
                            });

                            self.core.closeModal();
                            self.core.redirectToAction('members', {})

                        }, (error) => {
                            console.log(error);
                        }, () => {
                            $.toast({
                                heading: 'Medlems oprettelse',
                                text: 'Opretter ' + self.core.request.data.member.gamertag,
                                icon: 'info',
                                position: 'bottom-right'
                            });
                        });
                    }
                });

                // export members from list
                self.container.find('.export-members').click((e) => {
                    e.preventDefault();

                    self.core.sendRequest('dxl_export_members', 'GET', self.core.request.url, self.core.request.data, (response) => {
                        console.log(response)
                        const json = JSON.parse(response);
                        const filename = json.members.response;
            
                        if( filename ) {
                            var downloadLink = document.createElement('a');
                            downloadLink.setAttribute("download", filename);
                            downloadLink.setAttribute("href", "/" + filename);
                            console.log(downloadLink.download);
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                        }
                    }, (error) => {
                        console.log(error);
                    }, () => {
                        
                    });
                });

                // if the member has payed update the members payment status
                self.container.find('.has-payed-button').click(() => {
                    self.core.openModal('.hasPayedModal');

                    $('.payment-accepted-button').click(() => {
                        // update payed status on member
                        const member = $('.payment-accepted-button').data("member");
                        self.core.request.data.member = {
                            id: member
                        };

                        self.core.sendRequest('dxl_member_update_payed', 'POST', self.core.request.url, self.core.request.data, (response) => {
                            console.log(response)

                            const json = JSON.parse(response);
                            const hasError = self.core.checkForResponseError(json.member);

                            if( json.member.error === true ) {

                                $.toast({
                                    title: "Fejl",
                                    text: json.member.response,
                                    icon: "error",
                                    position: "bottom-right"
                                });

                                return false;
                            }

                            $.toast({
                                title: "Success",
                                text: json.member.response,
                                icon: 'success',
                                position: 'bottom-right'
                            });

                            self.core.closeModal();
                            self.core.redirectToAction('members', {
                                action: 'details',
                                id: self.core.request.data.member.id
                            });

                        }, (error) => {
                            console.log(error)
                        }, () => {
                            $.toast({
                                title: "Medlems betaling",
                                text: "Accepterer betaling vent veligst..",
                                icon: "info",
                                position: 'bottom-right'
                            });
                        })
                    });
                })

                self.container.find('.deactivate-member-button').click((e) => {
                    e.preventDefault();
                    self.core.openModal('.removePayedStatusModal');

                    $('.deactivate-member-payment').click((e) => {
                        e.preventDefault();

                        const member = $(".deactivate-member-payment").data('member');
                        self.core.request.data.member = {
                            id: member
                        };
                        
                        self.core.sendRequest(
                            'dxl_member_deactivate_payment',
                            'POST',
                            self.core.request.url,
                            self.core.request.data,
                            (response) => {
                                console.log(response)

                                const json = JSON.parse(response);
                                const hasError = self.core.checkForResponseError(json.member);

                                if( hasError ) {
                                    $.toast({
                                        title: "Error",
                                        text: json.member.response,
                                        icon: "error",
                                        position: "bottom-right"
                                    });

                                    return false;
                                }

                                $.toast({
                                    title: "Success",
                                    text: json.member.response,
                                    icon: "success",
                                    position: "bottom-right"
                                });

                                self.core.closeModal();
                                self.core.redirectToAction('members', {})
                            },
                            (error) => {
                                console.log(error)
                            },
                            () => {
                                $.toast({
                                    title: "Sender forespørgsel",
                                    text: "Deaktivere medlem vent venligst..",
                                    icon: "info",
                                    position: "bottom-right"
                                });
                            }
                        )
                    })
                })

                // activate member profile
                self.container.find('.activate-profile-button').click(() => {
                    const action = self.container.find('.activate-profile-button').data('action');
                    const member = self.container.find('.activate-profile-button').data('member');
                    self.core.request.data.member = {
                        action: action,
                        id: member
                    };

                    self.core.sendRequest('dxl_member_update_action', 'POST', self.core.request.url, self.core.request.data, (response) => {
                        console.log(response);

                        const json = JSON.parse(response);
                        const hasError = self.core.checkForResponseError(json.member);

                        if( hasError ) {
                            $.toast({
                                title: "Fejl",
                                text: json.member.response,
                                icon: 'error',
                                position: "bottom-right"
                            });
                            return false;
                        }

                        $.toast({
                            title: "Opdateret",
                            text: json.member.response,
                            icon: "success"
                        });

                        self.core.redirectToAction('members', {
                            action: 'details',
                            id: member
                        }) 

                    }, (error) => {
                        console.log(error)
                    }, () => {
                        $.toast({
                            text: "Aktivere profil",
                            icon: "info"
                        });
                    });
                })

                // deactivate member profile
                self.container.find('.deactivate-profile-button').click(() => {
                    const action = self.container.find('.deactivate-profile-button').data('action');
                    const member = self.container.find('.deactivate-profile-button').data('member');
                    console.log(member);
                    
                    self.core.request.data.member = {
                        id: member,
                        action: action
                    }

                    self.core.sendRequest(
                        'dxl_member_update_action',
                        'POST',
                        self.core.request.url,
                        self.core.request.data,
                        (response) => {
                            console.log(response)

                            const json = JSON.parse(response);
                            const hasError = self.core.checkForResponseError(json.member);

                            if( hasError ) {
                                $.toast({
                                    title: "Opdateret",
                                    text: json.member.response,
                                    icon: (json.member.error) ? 'error' : 'success',
                                    position: 'bottom-right'
                                });

                                return false;
                            }

                            $.toast({
                                title: "Opdateret",
                                text: json.member.response,
                                icon: (json.member.error) ? 'error' : 'success',
                                position: 'bottom-right'
                            });

                            self.core.redirectToAction('members', {
                                action: 'details',
                                id: member
                            })
                        },
                        (error) => {
                            console.log(error)
                        },
                        () => {
                            $.toast({
                                text: "Sender forespørgsel",
                                icon: "info",
                                position: 'bottom-right'
                            })
                        }
                    )
                });

                // assign trainer permissions to member
                self.container.find('.assign-trainer-permissions-btn').click(() => {
                    const action = self.container.find('.assign-trainer-permissions-btn').data('action');
                    const member = self.container.find('.assign-trainer-permissions-btn').data('member');

                    self.core.request.data.member = {
                        id: member,
                        action: action
                    };

                    self.core.sendRequest(
                        'dxl_member_update_action',
                        'POST',
                        self.core.request.url,
                        self.core.request.data,
                        (response) => {
                            console.log(response);

                            const json = JSON.parse(response);
                            const hasError = self.core.checkForResponseError(json.member);

                            if( hasError ) {
                                $.toast({
                                    title: "Fejl!",
                                    text: json.member.response,
                                    icon: "error",
                                    position: 'bottom-right'
                                });
                                return false;
                            }

                            $.toast({
                                title: "success!",
                                text: json.member.response,
                                icon: "success",
                                position: 'bottom-right'
                            })

                            self.core.redirectToAction('members', {
                                action: 'details',
                                id: member
                            })
                        },
                        (error) => {console.log(error)},
                        () => {
                            $.toast({
                                title: "Sender forespørgsel",
                                text: "Opdatere medlem",
                                icon: "info",
                                position: "bottom-right"
                            })
                        }
                    );
                });

                // remove trainer permissions from member
                self.container.find('.remove-trainer-permissions-btn').click(() => {
                    const action = self.container.find('.remove-trainer-permissions-btn').data('action');
                    const member = self.container.find('.remove-trainer-permissions-btn').data('member');
                
                    self.core.request.data.member = {
                        id: member,
                        action: action
                    }

                    self.core.sendRequest(
                        'dxl_member_update_action',
                        'POST',
                        self.core.request.url,
                        self.core.request.data,
                        (response) => {
                            console.log(response);

                            const json = JSON.parse(response);
                            const hasError = self.core.checkForResponseError(json.member);

                            if( hasError ) {
                                $.toast({
                                    title: "Fejl!",
                                    text: json.member.response,
                                    icon: "error",
                                    position: 'bottom-right'
                                });

                                return false;
                            }

                            $.toast({
                                title: "success!",
                                text: json.member.response,
                                icon: "success",
                                position: 'bottom-right'
                            })

                            self.core.redirectToAction('members', {action: 'details', id: member})
                        },
                        (error) => {console.log(error)},
                        () => {
                            $.toast({
                                title: "Sender forespørgsel",
                                text: "Opdatere medlem",
                                icon: "info",
                                position: "bottom-right"
                            })
                        }
                    );
                });

                // requesting tournament author permissions assigned to member
                self.container.find('.assign-tournament-permissions-btn').click((e) => {
                    e.preventDefault();
                    const action = self.container.find('.assign-tournament-permissions-btn').data('action');
                    const member = self.container.find('.assign-tournament-permissions-btn').data('member');

                    self.core.request.data.member = {
                        action: action,
                        id: member
                    };

                    self.core.sendRequest(
                        'dxl_member_update_action',
                        'POST',
                        self.core.request.url,
                        self.core.request.data,
                        (response) => {
                            console.log(response);

                            const json = JSON.parse(response);
                            const hasError = self.core.checkForResponseError(json.member);

                            if( hasError ) {
                                $.toast({
                                    title: "Fejl!",
                                    text: json.member.response,
                                    icon: "error",
                                    position: "bottom-right"
                                });
                                return false;
                            }

                            $.toast({
                                title: "Success",
                                text: json.member.response,
                                icon: "success",
                                position: "bottom-right"
                            });

                            self.core.redirectToAction('members', {action: 'details', id: member})
                        },
                        (error) => {console.log(error);},
                        () => {
                            $.toast({
                                text: "Opdatere rettigheder",
                                icon: "info",
                                position: "bottom-right"
                            })
                        }
                    )
                });

                // removing tournament author permissions from member
                self.container.find('.remove-tournament-permissions-btn').click(() => {
                    const action = self.container.find('.remove-tournament-permissions-btn').data('action');
                    const member = self.container.find('.remove-tournament-permissions-btn').data('member');

                    self.core.request.data.member = {
                        action: action,
                        id: member
                    };

                    self.core.sendRequest(
                        'dxl_member_update_action',
                        'POST',
                        self.core.request.url,
                        self.core.request.data,
                        (response) => {
                            console.log(response);

                            const json = JSON.parse(response);
                            const hasError = self.core.checkForResponseError(json.member);

                            if( hasError ) {
                                $.toast({
                                    title: "Fejl!",
                                    text: json.member.response,
                                    icon: "error",
                                    position: "bottom-right"
                                });
                                return false;
                            }

                            $.toast({
                                title: "Success",
                                text: json.member.response,
                                icon: "success",
                                position: "bottom-right"
                            });

                            self.core.redirectToAction('members', {action: 'details', id: member})
                        },
                        (error) => {console.log(error);},
                        () => {
                            $.toast({
                                text: "Opdatere rettigheder",
                                icon: "info",
                                position: "bottom-right"
                            })
                        }
                    )
                });

                // submitting update member action
                $('.submit-update-member').click((e) => {
                    e.preventDefault();
                    
                        console.log("test");
                        const form = $('.adminUpdateMemberForm');

                        self.core.request.data.member = {};
                        self.core.request.data.member.id = form.find('#member-id').val();
                        self.core.request.data.member.name = form.find('#member-name').val();
                        self.core.request.data.member.gamertag = form.find('#member-gamertag').val();
                        self.core.request.data.member.email = form.find('#member-email').val();
                        self.core.request.data.member.phone = form.find('#member-phone').val();
                        self.core.request.data.member.birthyear = form.find('#member-birthdate').val();
                        self.core.request.data.member.gender = form.find('#member-gender').val();
                        self.core.request.data.member.member_number = form.find('#member-number').val();
                        self.core.request.data.member.address = form.find('#member-address').val();
                        self.core.request.data.member.zipcode = form.find('#member-zipcode').val();
                        self.core.request.data.member.city = form.find('#member-town').val();
                        self.core.request.data.member.municipality = form.find('#member-municipality').val();
                        self.core.request.data.member.membership = form.find('#member-membership').val();
                        self.core.request.data.member.auto_renew = form.find('#member-renew:checked').val();

                        self.core.sendRequest('dxl_member_update', 'POST', self.core.request.url, self.core.request.data, (response) => {
                            console.log(response);

                            const json = JSON.parse(response);
                            const hasError = self.core.checkForResponseError(json.member);

                            if(hasError) {
                                $.toast({
                                    title: "Fejl",
                                    text: json.member.response,
                                    icon: "error",
                                    position: "bottom-right"
                                });

                                return false;
                            }

                            $.toast({
                                title: "Success",
                                text: json.member.response,
                                icon: "success",
                                position: 'bottom-right'
                            });

                            $('.key-Medlemsnummer').html(self.core.request.data.member.member_number);
                            $('.key-Gamertag').html(self.core.request.data.member.gamertag);
                            $('.key-Adresse').html(self.core.request.data.member.address);
                            $('.key-Postnummer').html(self.core.request.data.member.zipcode);
                            $('.key-Bynavn').html(self.core.request.data.member.city);
                            $('.key-Komunne').html(self.core.request.data.member.municipality);
                            $('.key-Email').html(self.core.request.data.member.email);
                            $('.key-Fødselsår').html(self.core.request.data.member.birthyear);

                            self.core.closeModal();
                        }, (error) => {
                            console.log(error)
                        }, () => {
                            $.toast({
                                title: "Opdatere medlem",
                                text: "Sender oplysninger til systemet vent venligst",
                                icon: "info",
                                position: "bottom-right"
                            })
                        })
                    });

                // delete member ressource from list
                self.container.find('.delete-member-button').click((e) => {
                    e.preventDefault();
                    const member = self.container.find('.delete-member-button').data('member');
                    const memberName = self.container.find('.delete-member-button').data('name');

                    self.core.request.data.member = {
                        id: member
                    }

                    self.core.sendRequest('dxl_member_delete', 'POST', self.core.request.url, self.core.request.data, (response) => {
                        console.log(response);

                        const member = JSON.parse(response).member;
                        const hasError = self.core.checkForResponseError(member);

                        if( hasError ) {
                            $.toast({
                                title: "Fejl",
                                text: member.response,
                                icon: "error",
                                position: "bottom-right"
                            });
                            return false;
                        }
                        
                        $.toast({
                            title: "success",
                            text: member.response,
                            icon: "success",
                            position: "bottom-right"
                        });

                        self.core.redirectToAction('members', {});

                    }, (error) => {
                        console.log(error)
                    }, () => {
                        $.toast({
                            title: "Sletter medlem",
                            text: "Sletter medlem: " + memberName,
                            icon: "info",
                            position: "bottom-right"
                        });
                    })
                })

                // creating new membership ressource
                self.container.find('.submit-create-membership').click((e) => {
                    e.preventDefault();
                    self.core.request.data.membership = {
                        name: self.container.find('#membership-name').val(),
                        length: self.container.find('#membership-length').val()
                    }

                    self.core.sendRequest('dxl_create_membership', 'POST', self.core.request.url, self.core.request.data, (response) => {
                        console.log(response);

                        const json = JSON.parse(response).membership;
                        const hasError = self.core.checkForResponseError(json)
                        if( hasError ) {
                            $.toast({
                                title: "Fejl",
                                text: json.response,
                                icon: "error",
                                position: "bottom-right"
                            });
                            return false;
                        }

                        $.toast({
                            title: "Succes",
                            text: json.response,
                            icon: "error",
                            position: "bottom-right"
                        });
                        self.core.closeModal();
                        self.core.redirectToAction('memberships', {});

                    }, (error) => {console.log(error)}, () => {
                        // before send callback
                        $.toast({
                            text: "Opretter kontingent",
                            icon: "info",
                            position: "bottom-right"
                        })
                    })
                })

                self.container.find('.delete-membership-button').click((e) => {
                    e.preventDefault();
                    const membership = self.container.find('.delete-membership-button').data('membership');
                    console.log(membership)

                    self.core.request.data.membership = {
                        id: membership
                    };

                    self.core.sendRequest('dxl_delete_membership', 'POST', self.core.request.url, self.core.request.data, (response) => {
                        console.log(response);
                    
                        const json = JSON.parse(response).membership;
                        const hasError = self.core.checkForResponseError(json);

                        if( hasError ) {
                            $.toast({
                                title: "Fejl",
                                text: json.response,
                                icon: "error",
                                position: "bottom-right"
                            });
                            return false;
                        }

                        $.toast({
                            title: "Fejl",
                            text: json.response,
                            icon: "error",
                            position: "bottom-right"
                        });
                        self.core.redirectToAction('memberships', {});

                    }, (error) => {console.log(error)}, () => {
                        $.toast({
                            text: "fjerner kontingent. vent venligst..",
                            icon: "info",
                            position: "bottom-right"
                        });
                    })
                })
            },
         }

         DXLAdminMember.init();
    });
})(jQuery, document);