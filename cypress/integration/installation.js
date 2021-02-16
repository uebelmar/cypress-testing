describe('Installation & Login', () => {
    it('should open installer', () => {
        cy.visit('http://spicecrm.local/frontend');
        cy.wait(7500);

    })
    it('should set backend', () => {
        cy.get('input[name=systemname]').type("My CRM");
        cy.get('input[name=systemurl]').type("http://spicecrm.local/backend");
        cy.get('select[name=systemproxy]').select('No');
        cy.get('select[name=systemdevmode]').select('Yes');
        cy.get("button[name=nextButton").click();
    })
    it('should skip system requirements', () => {
        cy.wait(1000);
        cy.get("button[name=nextButton]").click();
    })
    it('should accept license', () => {
        cy.wait(1000);
        cy.get("input[id=checkbox-1]").check({force: true});
        cy.get("button[name=nextButton]").click();
    })
    it('should select mysql as database type', () => {
        cy.wait(1000);
        cy.get("select[name=db_type]").select("mysql");
    })
    it('should populate mysql datas', () => {
        cy.wait(1000);
        const uuid = () => Cypress._.random(0, 1e6)
        const id = uuid()
        const testname = `cypress_${id}`
        cy.get("input[name=db_host_name]").type("localhost");
        cy.get("input[name=db_user_name]").type("root");
        cy.get("input[name=db_password]").type("root");
        cy.get("input[name=db_name]").type(testname);
        cy.get("button[name=nextButton]").click();
    })
    it('should populate fts server', () => {
        cy.wait(1000);
        cy.get("input[name=server]").type("localhost");
        cy.get("button[name=nextButton]").click();
    })
    it('should populate credentials', () => {
        cy.wait(1000);
        cy.get("input[name=password]").type("21Reasons!");
        cy.get("input[name=password_repeat]").type("21Reasons!");
        cy.get("input[name=first_name]").type("Max");
        cy.get("input[name=surname]").type("Mustermann");
        cy.get("input[name=email]").type("cypress@twentyreasons.com");
        cy.get("button[name=nextButton]").click();
    })
    it('should select german language', () => {
        cy.wait(1000);
        cy.get("select[name=language]").select("Deutsch");
        cy.get("button[name=nextButton]").click();
    })
    it('should start the installation process', () => {
        cy.wait(1000);
        cy.get("button[name=start_installation]").click();
        cy.wait(45000);
    })
    it('should login', () => {
        cy.get("input[name=username]").type("admin");
        cy.get("input[name=password]").type("21Reasons!");
        cy.get("button[type=submit]").click();
        cy.wait(7000);
        cy.get("span").contains("Wichtige Benutzer-Einstellungen ");
    })
})