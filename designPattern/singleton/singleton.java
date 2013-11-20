public class TicketMaker {
    private int ticket = 1000;
    public int getNextTicketNumber() {
        return ticket++;
    }
}

public class TicketMaker {
    private int ticket = 1000;
    private static TicketMaker singleton = new TicketMaker();
    // private修飾子を付けることで、外部からのインスタンスの生成を制限する
    private TicketMaker() {
    }

    public static TicketMaker getInstance() {
        return singleton;
    }

    public synchronized int getNextTicketNumber() {
        return ticket++;
    }
}

public class Triple {
    private static Triple[] triple = new Triple[] {
        new Triple(0),
        new Triple(1),
        new Triple(2),
    };
    private int id;
    private Triple(id) {
        obj.add[id, ]
    }

    public static Triple getInstance(int id) {
        return obj[id];
    }
}
