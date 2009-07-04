// Don't use this!!!
package ideas;

import javax.jdo.annotations.IdGeneratorStrategy;
import javax.jdo.annotations.IdentityType;
import javax.jdo.annotations.PersistenceCapable;
import javax.jdo.annotations.Persistent;
import javax.jdo.annotations.PrimaryKey;
import com.google.appengine.api.datastore.Text ;

@PersistenceCapable(identityType = IdentityType.APPLICATION,detachable="true")
public class JsonPDO {

    @PrimaryKey
    @Persistent(valueStrategy = IdGeneratorStrategy.IDENTITY)
    private Long id;
    
    @Persistent
    private String json;

    @Persistent
    private Text newJson;

    @Persistent
    private String login;

    public String getLogin() {
        return login ;
    }

    public String getJson() {
        return json;
    }

    public Text getNewJson() {
        return newJson;
    }
    
    public Long getId() {
        return id;
    }
        
    public void setLogin(String login) {
        this.login = login;
    }

    public void setJson(String json) {
        this.json = json;
    }

    public void setNewJson(Text json) {
        this.newJson = json;
    }

    public void setId(Long id) {
        this.id = id ;
    }
}
